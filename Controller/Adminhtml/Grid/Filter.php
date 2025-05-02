<?php
namespace Mage\Grid\Controller\Adminhtml\Grid;

use Genaker\MagentoMcpAi\Controller\Chat\Query as ChatQuery;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\ObjectManagerInterface;

class Filter extends Action
{
    protected $resultJsonFactory;
    protected $chatQuery;
    protected $om;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        ObjectManagerInterface $om
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->om = $om;
    }

    protected function _isAllowed()
    {
        return true;  // Or set proper ACL permission
    }

    public function execute(): ResultInterface
    {
        // dd($this->getRequest()->getParams());
        try {
            $query = $this->getRequest()->getParam('query');
            if (!$query) {
                throw new \Exception(__('No query provided'));
            }

            try {
                $this->chatQuery = $this->om->get(ChatQuery::class);
            } catch (\Exception $e) {
                $this->logger->error('Error getting Chat AI extension: ' . $e->getMessage());
                return $this->resultJsonFactory->create()->setData([
                    'success' => false,
                    'message' => __('An error occurred while processing your request. Please try again.')
                ]);
            }

            // Prepare context for grid filtering
            $storeContext = [
                'DataAPIUrl' => $this->getRequest()->getParam('DataUrl', ''),
                'store_id' => $this->getRequest()->getParam('store', 0),
                'website_id' => $this->getRequest()->getParam('website', 0)
            ];

            // Get AI response
            $aiResponse = $this->getAiResponse($query, $storeContext);

            $groupByFields = json_decode($aiResponse, true)['groupByFields'];
            $urlParams = json_decode($aiResponse, true)['filterUrl'];
            $explanation = json_decode($aiResponse, true)['explanation'];
            $confidence = json_decode($aiResponse, true)['confidence'];

            // Parse AI response to get filter suggestions
            $filters = $this->parseAiResponse($aiResponse);

            return $this->resultJsonFactory->create()->setData([
                'success' => true,
                'suggestion' => [
                    'filterUrl' => $urlParams ?? '',
                    'groupByFields' => $groupByFields ?? '',
                    'explanation' => $explanation ?? '',
                    'confidence' => $confidence ?? '',
                    'field' => $filters[0]['field'] ?? 'entity_id',
                    'value' => $filters[0]['value'] ?? $query,
                    'filters' => $filters
                ],
                'raw' => $aiResponse
            ]);
        } catch (\Exception $e) {
            return $this->resultJsonFactory->create()->setData([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get AI response for a query
     */
    private function getAiResponse($query, $storeContext)
    {
        try {
            // Get available grid fields and their types
            $gridFields = $this->getGridFields();

            // Get current filters from URL
            $currentFilters = $this->getCurrentFilters();

            // Get pagination info
            $pagination = $this->getPaginationInfo();

            // Get sorting info
            $sorting = $this->getSortingInfo();

            // Build enhanced system prompt
            $systemPrompt = 'You are a Magento Grid Filter Assistant. '
                . 'Your task is to analyze user queries and suggest appropriate filters for the grid.'
                . "\n\nRules:\n1. Only suggest filters that exist in the grid you you don't know ask for more context\n"
                . "2. Consider current filters and pagination\n"
                . "3. Provide multiple filter options when appropriate\n"
                . "4. If unsure, suggest multiple ask more context from user\n"
                . "5. Always respond in JSON format or ask more context from user\n"
                . "6. Respect field examples provided\n"
                . "7. Available Fields:\n" . json_encode($gridFields, JSON_PRETTY_PRINT) . "\n\nCurrent Context:\n" . json_encode([
                    'current_filters' => [],
                    'pagination' => $pagination,
                    'sorting' => $sorting,
                    'store_context' => $storeContext
                ], JSON_PRETTY_PRINT)
                . "\n\n8. Make sure you are having valid url for filterUrl \n\n"
                . "8.1 add new filters to the end of the url get parameters \n\n"
                . "8.2 add groupByFields if such is needed and allowed by the field config 'groupByField' => true, to the end of the url get parameters groupByFields=field1,field2 \n\n"
                . "9. make sure us are using correct domain and url path from the DatAPIurl . Thanks \n\n"
                . "9.1 url like key/gdgdgf/filter[entity_id]=value?page=1&pageSize=40 is not valid \n\n"
                . "9.2 url like key/gdgdgf/?filter[entity_id]=value&page=1&pageSize=40 is valid \n\n"
                . "9.3 if you have multiple filters like key/gdgdgf/?filter[entity_id]=value&filter[entity_id]=value2  make them like filter[entity_id][]=value \n\n"
                . "\n\nResponse Format:\n{\n \"filterUrl\": url with all filter parameters,\n \"groupByFields\": \"value\", \"filters\": [\n        {\n            \"field\": \"field_name\",\n     \"value\": \"filter_value\",\n            \"description\": \"Optional description of the filter\"\n        }\n    ],\n    \"explanation\": \"Optional explanation of the suggested filters\",\n    \"confidence\": \"high|medium|low\",\n    \"alternative_suggestions\": [\n        {\n            \"filters\": [...],\n            \"reason\": \"Why this alternative might be useful\"\n        }\n    ]\n}\n";

            // Use AgentoAI Chat Query to get response
            $response = $this->chatQuery->getAiResponse(
                $query,
                $storeContext,
                [],  // No conversation history needed for grid filtering
                $systemPrompt
            );

            return $response;
        } catch (\Exception $e) {
            throw new \Exception(__('Error processing AI request: %1', $e->getMessage()));
        }
    }

    /**
     * Get available grid fields and their types
     */
    private function getGridFields()
    {
        $fields = [
            'entity_id' => [
                'type' => 'number',
                'label' => 'Order ID',
                'multiple' => false,
                'alias' => 'entity id, order id , id',
                'filter_example_get_url_parameter' => 'filter[entity_id][]=value',
                'description' => 'Unique identifier for the record'
            ],
            'increment_id' => [
                'type' => 'string',
                'label' => 'Increment ID',
                'multiple' => false,
                'filter_example_get_url_parameter' => 'filter[increment_id][]=value',
                'description' => 'Order number or unique identifier'
            ],
            'status' => [
                'type' => 'select',
                'label' => 'Status',
                'multiple' => true,
                'filter_example_get_url_parameter' => 'filter[status][]=value',
                'options' => [
                    'pending' => 'Pending',
                    'processing' => 'Processing',
                    'complete' => 'Complete',
                    'canceled' => 'Canceled',
                    'holded' => 'Holded',
                    'rejected' => 'Rejected'
                ],
                'description' => 'Current status of the record'
            ],
            'created_at' => [
                'multiple' => false,
                'type' => 'date',
                'label' => 'Created At',
                'filter_example_get_url_parameter' => 'filter[created_at]=value',
                'description' => 'Date when the record was created'
            ],
            'grand_total' => [
                'multiple' => false,
                'type' => 'number',
                'label' => 'Grand Total',
                'alias' => 'grand_total, amount, sum, total',
                'filter_example_get_url_parameter' => 'filter[grand_total]=value',
                'description' => 'Total amount of the order'
            ],
            'customer_email' => [
                'groupByField' => true,
                'multiple' => false,
                'type' => 'string',
                'label' => 'Customer Email',
                'alias' => 'customer_email, email, user email, @email, @',
                'filter_example_get_url_parameter' => 'filter[customer_email][]=value',
                'description' => 'Customer email address'
            ],
            'updated_at' => [
                'multiple' => false,
                'type' => 'date',
                'label' => 'Updated At',
                'filter_example_get_url_parameter' => 'filter[updated_at]=value',
                'description' => 'Date when the record was last updated'
            ]
        ];

        return $fields;
    }

    /**
     * Get current filters from URL
     */
    private function getCurrentFilters()
    {
        $filters = [];
        $urlParams = $this->getRequest()->getParams();
        unset($urlParams['query']);
        foreach ($urlParams as $key => $value) {
            $field = str_replace('filter[', '', $key);
            $field = str_replace(']', '', $field);
            $filters[$field] = $value;
        }

        return $filters;
    }

    /**
     * Get pagination info
     */
    private function getPaginationInfo()
    {
        return [
            'current_page' => $this->getRequest()->getParam('page', 1),
            'page_size' => $this->getRequest()->getParam('pageSize', 20),
            'total_records' => $this->getRequest()->getParam('totalRecords', 0)
        ];
    }

    /**
     * Get sorting info
     */
    private function getSortingInfo()
    {
        return [
            'sort_field' => $this->getRequest()->getParam('sortField', null),
            'sort_order' => $this->getRequest()->getParam('sortOrder', null)
        ];
    }

    /**
     * Parse AI response to get filter suggestions
     */
    private function parseAiResponse($response)
    {
        try {
            $data = json_decode($response, true);
            if (isset($data['filters']) && is_array($data['filters'])) {
                return $data['filters'];
            }
            throw new \Exception(__('Invalid AI response format'));
        } catch (\Exception $e) {
            // If parsing fails, create a basic filter
            return [
                [
                    'filterUrl' => '',
                    'field' => 'entity_id',
                    'operator' => 'like',
                    'value' => '%' . $response . '%'
                ]
            ];
        }
    }
}
