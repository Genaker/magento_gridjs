# Mage Grid Module

A powerful and flexible grid system for Magento 2 that provides an easy way to create and manage data grids in the admin panel.

## Features

- **Flexible Data Sources**: Support for both collection-based and SQL-based data sources
- **AJAX Support**: Built-in AJAX functionality for smooth data loading
- **Customizable Fields**: Easy field configuration and management
- **Built-in Filtering**: Advanced filtering capabilities
- **Pagination**: Automatic pagination handling
- **Frontend & Backend Support**: Works in both frontend and backend contexts
- **Responsive Design**: Mobile-friendly grid layout
- **Customizable Templates**: Easy to override and customize templates

## Why Our Grid is Better Than Magento's Legacy Grid

Our grid implementation offers several advantages over Magento's legacy grid system:

1. **Modern Technology Stack**:
   - Uses GridJS, a modern JavaScript grid library
   - Built with Preact for efficient rendering
   - Supports modern JavaScript features and async/await

2. **Better Performance**:
   - Lighter weight than Magento's legacy grid
   - Faster rendering and data loading
   - Reduced server load with client-side processing

3. **Enhanced Flexibility**:
   - Easy to customize columns and cells
   - Support for both collection and SQL-based data sources
   - Simple integration with Magento's UI components

4. **Improved Developer Experience**:
   - Cleaner code structure
   - Better separation of concerns
   - Easier to extend and maintain

5. **Better User Experience**:
   - Smoother interactions
   - More responsive interface
   - Better mobile support

6. **Advanced Features**:
   - Built-in AJAX support
   - Advanced filtering capabilities
   - Custom cell renderers
   - Dynamic data loading

7. **Integration with Magento**:
   - Seamless integration with Magento's admin panel
   - Support for Magento's UI components
   - Compatible with Magento's theming system

## Why This Grid is AI-First

Mage Grid is designed from the ground up to be **AI-first**. This means:

- The codebase is structured for easy understanding and modification by AI coding assistants (like Cursor, GitHub Copilot, or ChatGPT).
- Naming conventions, modularity, and documentation are optimized for AI-driven code generation and extension.
- The grid is built to be easily extensible and customizable with minimal manual intervention, making it ideal for rapid prototyping and iterative development with AI tools.
- Simple strait forward architecture and access to code with examples and documenttion makes it straightforward for LLMs to read, understand, and even improve your components. AI-Ready: Open code for LLMs to read, understand, and improve.

The design of the Mage Grids makes it easy for AI tools to work with your code. Its open code and simple API allow AI models to read, understand, and generate new components.

### What Makes It AI-First?
- **Consistent, clear naming**: Classes, methods, and templates are named for discoverability and semantic search.
- **Separation of concerns**: Logic, templates, and configuration are cleanly separated, so AI can suggest or generate changes in isolation.
- **Extensive inline documentation**: Comments and docblocks are written to help both humans and AI understand intent and usage.
- **Configurable via XML and DI**: Most features (fields, processors, templates) are configured in XML or DI, so AI can suggest changes without deep PHP edits.
- **Composable processors**: The data processor system is designed for easy extension and chaining, which is ideal for AI-driven code generation.
- **Template-driven UI**: UI and JS are in templates, so AI can generate or modify UI logic without touching backend code.

## How to Extend Mage Grid Using Cursor or AI

You can use AI tools like Cursor to:
- Add new data processors (e.g., for custom formatting, badges, or links)
- Add or modify grid fields and templates
- Inject new JS/HTML for popups, modals, or custom actions
- Refactor or optimize code for performance or maintainability

### Example: Adding a Custom Data Processor with Cursor
1. **Describe your goal in Cursor:**
   - "Add a processor that renders the 'priority' field as a colored badge."
2. **Let Cursor search for DataProcessorInterface and StatusProcessor.**
3. **Cursor generates a new PriorityProcessor.php:**
   - Implements the interface, adds color logic, and registers in di.xml.
4. **Update your layout XML to map the 'priority' field to the new processor.**

### Example: Adding a Popup with AI
1. **Describe your goal:**
   - "Add a popup that shows full row details when the 'Order Number' cell is clicked."
2. **AI suggests a new JS function and a template snippet.**
3. **Add the JS to an additional-html.phtml template and configure it in layout XML.**
4. **AI updates the grid template to include the new popup logic.**

### Practical Steps for AI-Driven Extension
- Use Cursor's semantic search to find relevant classes, templates, or config files.
- Use inline comments and docblocks to guide AI suggestions.
- Use the modular template and processor system to add or override features without deep code changes.
- Test changes iteratively, letting AI suggest fixes or improvements.

**Tip:**
- The more you describe your intent (in comments, commit messages, or AI prompts), the better the AI can help you extend or refactor the grid.

### AI Assistant Integration

The Mage Grid module includes an advanced AI Assistant that helps users interact with the grid using natural language queries. The AI Assistant:

- **Natural Language Filtering**: Users can type queries in natural language (e.g., "show orders from last week", "find orders with status pending") and the AI will convert them into appropriate filters.
- **Terminal-like Interface**: When users type "select" or similar SQL-like keywords, the input field transforms into a terminal-like interface with green text on black background and a blinking cursor.
- **Smart Suggestions**: Provides alternative filter suggestions when the query is ambiguous or when multiple filtering options are available.
- **Context-Aware**: Takes into account current filters, pagination, and sorting when suggesting new filters.
- **Error Handling**: Gracefully handles unclear queries and asks for clarification when needed.
- **Performance Optimized**: Caches frequent queries and optimizes filter combinations for better performance.


## Installation

1. Copy the module to your Magento installation:
```bash
cp -r app/code/Mage/Grid /path/to/magento/app/code/
```

2. Enable the module:
```bash
bin/magento module:enable Mage_Grid
```

3. Run setup upgrade:
```bash
bin/magento setup:upgrade
```

4. Clear cache:
```bash
bin/magento cache:clean
```

## Usage

### Basic Grid Setup

1. Create a layout file (e.g., `view/adminhtml/layout/your_module_grid_index.xml`):
```xml
<?xml version="1.0"?>
<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">
    <body>
        <referenceContainer name="content">
            <block class="Mage\Grid\Block\MageGridBlock" name="your_grid">
                <arguments>
                    <argument name="viewModel" xsi:type="object">Mage\Grid\ViewModel\GenericViewModelGrid</argument>
                    <argument name="collectionClass" xsi:type="string">Your\Module\Model\ResourceModel\YourCollection</argument>
                    <argument name="fields" xsi:type="array">
                        <item name="id" xsi:type="string">ID</item>
                        <item name="name" xsi:type="string">Name</item>
                        <item name="created_at" xsi:type="string">Created At</item>
                    </argument>
                </arguments>
            </block>
        </referenceContainer>
    </body>
</page>
```

2. Create a controller (e.g., `Controller/Adminhtml/YourGrid/Index.php`):
```php
<?php
namespace Your\Module\Controller\Adminhtml\YourGrid;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Your_Module::your_grid');
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Your_Module::your_grid');
        $resultPage->getConfig()->getTitle()->prepend(__('Your Grid Title'));
        return $resultPage;
    }
}
```

### Custom Column Cells with Preact

The module uses GridJS with Preact for rendering custom column cells. Here's how to create custom column cells:

1. **Basic Custom Column**:
```javascript
require(['jquery', 'gridjs'], function($, grid) {
    jQuery("#yourGrid").Grid({
        columns: [
            'Name',
            'Email',
            {
                name: 'Actions',
                formatter: (_, row) => html`<button onclick="editItem(${row.cells[0].data})">Edit</button>`
            }
        ],
        data: [
            ['John Doe', 'john@example.com'],
            ['Jane Smith', 'jane@example.com']
        ]
    });
});
```

2. **Complex Custom Column with Magento Integration**:
```javascript
require(['jquery', 'gridjs'], function($, grid) {
    jQuery("#yourGrid").Grid({
        columns: [
            'Order ID',
            'Customer',
            {
                name: 'Status',
                formatter: (cell, row) => {
                    const status = cell;
                    const color = status === 'Complete' ? 'green' : 'red';
                    return html`<span style="color: ${color}">${status}</span>`;
                }
            },
            {
                name: 'Actions',
                formatter: (_, row) => {
                    const orderId = row.cells[0].data;
                    return html`
                        <div class="actions">
                            <button onclick="viewOrder('${orderId}')">View</button>
                            <button onclick="editOrder('${orderId}')">Edit</button>
                            <button onclick="deleteOrder('${orderId}')">Delete</button>
                        </div>
                    `;
                }
            }
        ],
        data: [
            ['10001', 'John Doe', 'Complete'],
            ['10002', 'Jane Smith', 'Pending']
        ]
    });
});
```

3. **Custom Column with AJAX Data**:
```javascript
require(['jquery', 'gridjs'], function($, grid) {
    jQuery("#yourGrid").Grid({
        columns: [
            'Order ID',
            {
                name: 'Customer Details',
                formatter: async (_, row) => {
                    const orderId = row.cells[0].data;
                    const response = await fetch(`/rest/V1/orders/${orderId}`);
                    const data = await response.json();
                    return html`
                        <div class="customer-details">
                            <strong>${data.customer_firstname} ${data.customer_lastname}</strong>
                            <br/>
                            <small>${data.customer_email}</small>
                        </div>
                    `;
                }
            }
        ],
        data: [
            ['10001'],
            ['10002']
        ]
    });
});
```

4. **Custom Column with Magento UI Components**:
```javascript
require(['jquery', 'gridjs', 'Magento_Ui/js/modal/alert'], function($, grid, alert) {
    jQuery("#yourGrid").Grid({
        columns: [
            'Order ID',
            {
                name: 'Actions',
                formatter: (_, row) => {
                    const orderId = row.cells[0].data;
                    return html`
                        <div class="actions">
                            <button onclick="showOrderDetails('${orderId}')">Details</button>
                        </div>
                    `;
                }
            }
        ],
        data: [
            ['10001'],
            ['10002']
        ]
    });

    window.showOrderDetails = function(orderId) {
        alert({
            title: 'Order Details',
            content: `Loading order ${orderId}...`,
            actions: {
                always: function() {
                    // Load order details via AJAX
                }
            }
        });
    };
});
```

5. **Custom Column with Magento Form Elements**:
```javascript
require(['jquery', 'gridjs'], function($, grid) {
    jQuery("#yourGrid").Grid({
        columns: [
            'Order ID',
            {
                name: 'Status',
                formatter: (cell, row) => {
                    const orderId = row.cells[0].data;
                    return html`
                        <select onchange="updateStatus('${orderId}', this.value)">
                            <option value="pending" ${cell === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="processing" ${cell === 'processing' ? 'selected' : ''}>Processing</option>
                            <option value="complete" ${cell === 'complete' ? 'selected' : ''}>Complete</option>
                        </select>
                    `;
                }
            }
        ],
        data: [
            ['10001', 'pending'],
            ['10002', 'processing']
        ]
    });
});
```

### Configuration Options

The grid supports various configuration options:

1. **Collection-based Grid**:
```xml
<argument name="collectionClass" xsi:type="string">Your\Module\Model\ResourceModel\YourCollection</argument>
```

2. **SQL-based Grid**:
```xml
<argument name="tableName" xsi:type="string">your_table_name</argument>
```

3. **Field Configuration**:
```xml
<argument name="fields" xsi:type="array">
    <item name="field_name" xsi:type="string">Field Label</item>
</argument>
```

4. **Pagination**:
```xml
<argument name="pageSize" xsi:type="number">20</argument>
```

5. **Filters**:
```xml
<argument name="showFilters" xsi:type="boolean">true</argument>
```

### Customization

1. **Override Template**:
Create your own template at `view/adminhtml/templates/grid/grid-component.phtml`

2. **Custom ViewModel**:
Extend `GenericViewModelGrid` to add custom functionality:
```php
<?php
namespace Your\Module\ViewModel;

use Mage\Grid\ViewModel\GenericViewModelGrid;

class YourCustomViewModel extends GenericViewModelGrid
{
    // Add your custom methods here
}
```

### Customizing the Grid Component Template

The module uses a template file at `view/adminhtml/templates/grid/grid-component.phtml` that you can customize. Here are some examples:

1. **Basic Custom Column with Formatter**:
```php
<?php
// In your layout file
<block class="Mage\Grid\Block\GenericGrid" name="your_grid">
    <arguments>
        <argument name="fields" xsi:type="array">
            <item name="id" xsi:type="string">ID</item>
            <item name="name" xsi:type="string">Name</item>
            <item name="status" xsi:type="string">Status</item>
        </argument>
    </arguments>
</block>
```

```javascript
// In your template
const grid = new Grid({
    columns: [
        'ID',
        'Name',
        {
            name: 'Status',
            formatter: (cell) => {
                const color = cell === 'Active' ? 'green' : 'red';
                return html`<span style="color: ${color}">${cell}</span>`;
            }
        }
    ],
    data: [
        ['1', 'John Doe', 'Active'],
        ['2', 'Jane Smith', 'Inactive']
    ],
    pagination: {
        enabled: true,
        limit: 20
    },
    sort: true,
    search: true
}).render(document.getElementById('wrapper'));
```

2. **Custom Column with Actions**:
```javascript
const grid = new Grid({
    columns: [
        'ID',
        'Name',
        {
            name: 'Actions',
            formatter: (_, row) => {
                const id = row.cells[0].data;
                return html`
                    <div class="actions">
                        <button onclick="viewItem('${id}')">View</button>
                        <button onclick="editItem('${id}')">Edit</button>
                        <button onclick="deleteItem('${id}')">Delete</button>
                    </div>
                `;
            }
        }
    ],
    data: [
        ['1', 'John Doe'],
        ['2', 'Jane Smith']
    ]
}).render(document.getElementById('wrapper'));

// Add your action functions
window.viewItem = function(id) {
    // Your view logic
};

window.editItem = function(id) {
    // Your edit logic
};

window.deleteItem = function(id) {
    // Your delete logic
};
```

3. **Custom Column with Magento UI Components**:
```javascript
require(['jquery', 'gridjs', 'Magento_Ui/js/modal/alert'], function($, grid, alert) {
    const gridInstance = new Grid({
        columns: [
            'ID',
            'Name',
            {
                name: 'Actions',
                formatter: (_, row) => {
                    const id = row.cells[0].data;
                    return html`
                        <button onclick="showDetails('${id}')">Details</button>
                    `;
                }
            }
        ],
        data: [
            ['1', 'John Doe'],
            ['2', 'Jane Smith']
        ]
    }).render(document.getElementById('wrapper'));

    window.showDetails = function(id) {
        alert({
            title: 'Item Details',
            content: `Loading details for item ${id}...`,
            actions: {
                always: function() {
                    // Your AJAX logic here
                }
            }
        });
    };
});
```

4. **Custom Column with Dynamic Data Loading**:
```javascript
const grid = new Grid({
    columns: [
        'ID',
        {
            name: 'Details',
            formatter: async (_, row) => {
                const id = row.cells[0].data;
                try {
                    const response = await fetch(`/rest/V1/items/${id}`);
                    const data = await response.json();
                    return html`
                        <div class="item-details">
                            <strong>${data.name}</strong>
                            <br/>
                            <small>${data.description}</small>
                        </div>
                    `;
                } catch (error) {
                    return html`<span class="error">Error loading details</span>`;
                }
            }
        }
    ],
    data: [
        ['1'],
        ['2']
    ]
}).render(document.getElementById('wrapper'));
```

5. **Custom Column with Form Elements**:
```javascript
const grid = new Grid({
    columns: [
        'ID',
        'Name',
        {
            name: 'Status',
            formatter: (cell, row) => {
                const id = row.cells[0].data;
                return html`
                    <select onchange="updateStatus('${id}', this.value)">
                        <option value="pending" ${cell === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="processing" ${cell === 'processing' ? 'selected' : ''}>Processing</option>
                        <option value="complete" ${cell === 'complete' ? 'selected' : ''}>Complete</option>
                    </select>
                `;
            }
        }
    ],
    data: [
        ['1', 'John Doe', 'pending'],
        ['2', 'Jane Smith', 'processing']
    ]
}).render(document.getElementById('wrapper'));

window.updateStatus = function(id, status) {
    // Your status update logic
    console.log(`Updating status for ${id} to ${status}`);
};
```

6. **Custom Column with Filtering**:
```javascript
const grid = new Grid({
    columns: [
        'ID',
        'Name',
        {
            name: 'Status',
            formatter: (cell) => {
                const color = cell === 'Active' ? 'green' : 'red';
                return html`<span style="color: ${color}">${cell}</span>`;
            }
        }
    ],
    data: [
        ['1', 'John Doe', 'Active'],
        ['2', 'Jane Smith', 'Inactive']
    ],
    search: true,
    pagination: {
        enabled: true,
        limit: 20
    },
    sort: true,
    resizable: true
}).render(document.getElementById('wrapper'));

// Add custom filter handling
document.getElementById('filter-submit').addEventListener('click', function() {
    const filters = {};
    document.querySelectorAll('[id^="filter-"]').forEach(input => {
        const field = input.id.replace('filter-', '');
        filters[field] = input.value;
    });
    // Your filter logic here
    console.log('Filters:', filters);
});
```

## Security

The module includes built-in security features:
- ACL (Access Control List) integration
- Role-based access control
- XSS protection
- CSRF protection

## Support

For support, please contact:
- Email: support@mage.com
- GitHub Issues: [Create an issue](https://github.com/your-repo/mage-grid/issues)

## License

This module is licensed under the [MIT License](LICENSE).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

# Magento 2 Data GridsJS
**GridJS** and **DataTable** integration with Magento 2

Grid.js(https://gridjs.io/) is a Free and open-source JavaScript table plugin. It works with most JavaScript frameworks, including React, Angular, Vue and VanillaJs.

Grid.js uses Preact to render the elements and that means that you can take advantage of Preact's Virtual DOM and render complex cells.

**DataTable** integration with magento 

**DataTables** (https://datatables.net/) is a Javascript HTML table-enhancing library. It is a highly flexible tool, built upon the foundations of progressive enhancement, that adds all of these advanced features to any HTML table.

At First, GridJS was integrated, but after DataTable was added. 

All extensions come as jQuery plugins but can work independently. 

I also have **AG Grid** integration for Magento 2 but it is not part of this open source solution.
The Best JavaScript Grid in the World
The professional choice for developers building enterprise applications

This and all other magento extensions based on this revolutionary extension:
https://github.com/Genaker/reactmagento2
it allows running magento without MAgento broken JS and uses better modern solutions 

# Installation:
```
composer require mage/gridjs
```
# usage in Admin Area:
```
require(['jquery','gridjs']){() =>
jQuery('#lastOrdersGrid_table').Grid()
}
```
Examples:
After installation, examples will be embedded at the bottom of the admin dashboard 

![image](https://github.com/Genaker/magento_gridjs/assets/9213670/f5a78899-35be-40be-bfd9-dc3385c56548)

![image](https://github.com/Genaker/magento_gridjs/assets/9213670/f0c58b87-f588-4dfa-b576-c5811aa7df46)

## Source code of the grid

```
<h1> Grid JS</h1>

<script type="module">
//Modern libs don't work with this magento shit so we are using import.
import { html } from 'preact';

require(['jquery', 'gridjs'], function($, grid) {
  jQuery("div#myTable").Grid({
    columns: ['Name', 'Email', 'Phone Number' , { 
        name: 'Actions',
        formatter: (_, row) => html`<a href="."><button> EDIT </button></a>`
      }
      ],
    pagination: {
    limit: 2
    },
    search: true,
    sort: true,
    resizable: true,
    data: [
      ['John', 'john@example.com', '(353) 01 222 3333'],
      ['Mark', 'mark@gmail.com',   '(01) 22 888 4444'],
      ['Egor', 'egorshitikov@gmail.com',   '<span>NONE </span>']
    ]
  });
})
</script>

<div id="myTable"></div>

<h1> DataTable </h1>
<script>

require(['jquery', 'DataTable'], function($) {
  var dataSet = [
    ['Tiger Nixon', 'System Architect', 'Edinburgh', '5421', '2011/04/25', '$320,800'],
    ['Garrett Winters', 'Accountant', 'Tokyo', '8422', '2011/07/25', '$170,750'],
  ....
];
 
$('#example').DataTable({
    columns: [
        { title: 'Name' },
        { title: 'Position' },
        { title: 'Office' },
        { title: 'Extn.' },
        { title: 'Start date' },
        { title: 'Salary' }
    ],
    data: dataSet
});
})

</script>


<table id="example" class="display" width="100%"></table>
```
# More Examples GridJS

## In this examples, we load the data from an existing HTML table

Grid.js can also convert an HTML table. Simply select the table with jQuery and call Grid:

$("table#myTable").Grid();

You can pass all Grid.js configs to the Grid function. See Grid.js Config for more details.

##  HTML in cells 

Then you can use that in formatter function or directly in data array:


```
const grid = new Grid({
  columns: [
      { 
        name: 'Name',
        formatter: (cell) => html`<b>${cell}</b>`
      },
      'Email',
      { 
        name: 'Actions',
        formatter: (_, row) => html`<a href='mailto:${row.cells[1].data}'>Email</a>`
      },
   ],
  data: Array(5).fill().map(x => [
    faker.name.findName(),
    faker.internet.email(),
    null
  ])
});
```

## Import server-side data
You can use the server property to load data from a remote server and populate the table:

```
const grid = new Grid({
  columns: ['Name', 'Language', 'Released At', 'Artist'],
  server: {
    url: 'https://api.scryfall.com/cards/search?q=Inspiring',
    then: data => data.data.map(card => [card.name, card.lang, card.released_at, card.artist])
  } 
});
```

## Server Side Pagination
Add server property to the pagination config to enable server-side pagination. Also, make sure the total property is correctly defined in the main server config block

```
const grid = new Grid({
  columns: ['Pokemon', 'URL'],
  pagination: {
    limit: 5,
    server: {
      url: (prev, page, limit) => `${prev}?limit=${limit}&offset=${page * limit}`
    }
  },
  server: {
    url: 'https://pokeapi.co/api/v2/pokemon',
    then: data => data.results.map(pokemon => [
      pokemon.name, html(`<a href='${pokemon.url}'>Link to ${pokemon.name}</a>`)
    ]),
    total: data => data.count
  } 
});
```

# DataTable example 

## Loading data
Ajax data is loaded by DataTables simply by using the ajax option to set the URL for where the Ajax request should be made. For example, the following shows a minimal configuration with Ajax sourced data:

```
$('#myTable').DataTable( {
    ajax: '/api/myData'
} );
```

## JSON data source

When considering Ajax loaded data for DataTables we almost always are referring to a JSON payload - i.e. the data that is returned from the server is in a JSON data structure. This is because the JSON is derived from Javascript and it therefore naturally plays well with Javascript libraries such as DataTables.

## Non-jQuery options
If you are initialising DataTables through the new DataTable() option available in DataTables 1.11, you can pass configuration options in using the second parameter of the constructor:

```
new DataTable( '#example', {
    paging: false,
    scrollY: 400
} );
```

## Data rendering
Data within DataTables can be easily rendered to add graphics or colour to your tables, as demonstrated in the example on this page. These examples make use of columns.render to customise the cells in three ways:
![image](https://github.com/Genaker/magento_gridjs/assets/9213670/cb87d61b-e38b-46f4-8b97-f959ac55efd8)
```
$('#example').DataTable({
    ajax: '../ajax/data/objects_salary.txt',
    columns: [
        {
            data: 'name'
        },
        {
            data: 'position',
            render: function (data, type) {
                if (type === 'display') {
                    let link = 'https://datatables.net';
 
                    if (data[0] < 'H') {
                        link = 'https://cloudtables.com';
                    }
                    else if (data[0] < 'S') {
                        link = 'https://editor.datatables.net';
                    }
 
                    return '<a href="' + link + '">' + data + '</a>';
                }
 
                return data;
            }
        },
        {
            className: 'f32', // used by world-flags-sprite library
            data: 'office',
            render: function (data, type) {
                if (type === 'display') {
                    let country = '';
 
                    switch (data) {
                        case 'Argentina':
                            country = 'ar';
                            break;
                        case 'Edinburgh':
                            country = '_Scotland';
                            break;
                        case 'London':
                            country = '_England';
                            break;
                        case 'New York':
                        case 'San Francisco':
                            country = 'us';
                            break;
                        case 'Sydney':
                            country = 'au';
                            break;
                        case 'Tokyo':
                            country = 'jp';
                            break;
                    }
 
                    return '<span class="flag ' + country + '"></span> ' + data;
                }
 
                return data;
            }
        },
        {
            data: 'extn',
            render: function (data, type, row, meta) {
                return type === 'display'
                    ? '<progress value="' + data + '" max="9999"></progress>'
                    : data;
            }
        },
        {
            data: 'start_date'
        },
        {
            data: 'salary',
            render: function (data, type) {
                var number = $.fn.dataTable.render
                    .number(',', '.', 2, '$')
                    .display(data);
 
                if (type === 'display') {
                    let color = 'green';
                    if (data < 250000) {
                        color = 'red';
                    }
                    else if (data < 500000) {
                        color = 'orange';
                    }
 
                    return (
                        '<span style="color:' +
                        color +
                        '">' +
                        number +
                        '</span>'
                    );
                }
 
                return number;
            }
        }
    ]
});
```

## Data Processors System

Mage Grid uses a flexible and extensible **data processor** system to transform and format grid cell values before rendering. This allows you to easily customize how each field is displayed, including adding HTML, formatting dates, prices, statuses, and more.

### Dual Processor Configuration

The module supports two ways to configure data processors:

1. **Layout XML Configuration** (`grid_grid_index.xml`):
```xml
<block class="Mage\Grid\Block\GenericGrid" name="grid_generic_grid" template="Mage_Grid::grid/grid-component.phtml">
    <arguments>
        <argument name="dataProcessors" xsi:type="array">
            <item name="status" xsi:type="object">Mage\Grid\Model\DataProcessor\StatusProcessor</item>
            <item name="grand_total" xsi:type="object">Mage\Grid\Model\DataProcessor\PriceProcessor</item>
        </argument>
    </arguments>
</block>
```

2. **DI Configuration** (`di.xml`):
```xml
<type name="Mage\Grid\ViewModel\GenericViewModelGrid">
    <arguments>
        <argument name="dataProcessors" xsi:type="array">
            <item name="customer_email" xsi:type="object">Mage\Grid\Model\DataProcessor\ObfuscateProcessor</item>
            <item name="created_at" xsi:type="object">Mage\Grid\Model\DataProcessor\DateProcessor</item>
            <item name="grand_total" xsi:type="object">Mage\Grid\Model\DataProcessor\PriceProcessor</item>
        </argument>
    </arguments>
</type>
```

### Processor Resolution Order

1. Layout XML processors take precedence over DI configuration
2. If no processor is found in either location, the default processor is used
3. Processors can be chained using the `ChainProcessor`

### Example: Combined Configuration

```xml
<!-- grid_grid_index.xml -->
<block class="Mage\Grid\Block\GenericGrid" name="grid_generic_grid">
    <arguments>
        <argument name="dataProcessors" xsi:type="array">
            <!-- Grid-specific processors -->
            <item name="status" xsi:type="object">Mage\Grid\Model\DataProcessor\StatusProcessor</item>
        </argument>
    </arguments>
</block>

<!-- di.xml -->
<type name="Mage\Grid\ViewModel\GenericViewModelGrid">
    <arguments>
        <argument name="dataProcessors" xsi:type="array">
            <!-- Global processors -->
            <item name="customer_email" xsi:type="object">Mage\Grid\Model\DataProcessor\ObfuscateProcessor</item>
            <item name="created_at" xsi:type="object">Mage\Grid\Model\DataProcessor\DateProcessor</item>
        </argument>
    </arguments>
</type>
```

### Processor Types

1. **Default Processors**:
   - `DefaultProcessor`: Basic HTML escaping
   - `StatusProcessor`: Status badge rendering
   - `PriceProcessor`: Price formatting
   - `DateProcessor`: Date/time formatting
   - `ObfuscateProcessor`: Data obfuscation

2. **Custom Processors**:
   - Implement `Mage\Grid\Api\DataProcessorInterface`
   - Register in either layout XML or DI configuration
   - Can be grid-specific or global

### Example: Custom Processor

```php
namespace Your\Module\Model\DataProcessor;

use Mage\Grid\Api\DataProcessorInterface;

class CustomProcessor implements DataProcessorInterface
{
    public function process($field, $value, $row)
    {
        // Your custom processing logic
        return $processedValue;
    }
}
```

### Chain Processor

The `ChainProcessor` allows you to combine multiple processors:

```xml
<type name="Mage\Grid\Model\DataProcessor\ChainProcessor">
    <arguments>
        <argument name="processors" xsi:type="array">
            <item name="escape" xsi:type="object">Mage\Grid\Model\DataProcessor\DefaultProcessor</item>
            <item name="format" xsi:type="object">Mage\Grid\Model\DataProcessor\PriceProcessor</item>
        </argument>
    </arguments>
</type>
```

### Best Practices

1. Use layout XML processors for grid-specific customizations
2. Use DI configuration for global processors
3. Keep processors focused on a single responsibility
4. Use the chain processor for complex transformations
5. Document processor behavior and dependencies

## Additional HTML/JS Templates (Popups, Custom Scripts, etc.)

Mage Grid allows you to inject additional HTML or JavaScript into your grid page using configurable template files. This is useful for adding custom popups, event handlers, or any extra markup/scripts you need for your grid.

### How to Use

1. **Create your additional template(s):**
   - Place your custom HTML/JS in `.phtml` files, e.g.:
     - `view/adminhtml/templates/grid/additional-html.phtml`
     - `view/adminhtml/templates/grid/another-additional-html.phtml`

2. **Configure in layout XML:**
   - You can specify one or more additional templates using the `additional_html_templates` argument:
   ```xml
   <block class="Mage\Grid\Block\GenericGrid" name="your.grid.block" template="Mage_Grid::grid/grid-component.phtml">
       <arguments>
           <argument name="additional_html_templates" xsi:type="array">
               <item name="popupJS" xsi:type="string">Mage_Grid::grid/additional-html.phtml</item>
               <item name="custom2" xsi:type="string">Mage_Grid::grid/another-additional-html.phtml</item>
           </argument>
       </arguments>
   </block>
   ```
   - The block will render each template in order. If a template is missing, an error message will be shown for that template.

3. **Access in your main grid template:**
   - In your main grid template (e.g. `grid-component.phtml`), output the additional HTML:
   ```php
   <?= $block->getAditionalHTML() ?>
   ```

### Example: Popup Event Handler Integration

If you want to add a popup that shows row details when a status badge is clicked, your `additional-html.phtml` might look like:

```html
<script>

// Re-attach after every Grid.js update
if (window.grid) {
    grid.on('ready', attachStatusBadgePopup);
    grid.on('update', attachStatusBadgePopup);
}
</script>
```

**Important:**
- If your grid data is updated dynamically (e.g. via AJAX or Grid.js), you must re-attach event handlers after each update. The above example does this using Grid.js events.
- You can include as many additional templates as you need, and each can contain its own scripts or markup.

### Error Handling
- If a specified template does not exist, a user-friendly error message will be shown in the grid output.

## Filter Types

The Mage Grid module supports several types of filters that can be configured via layout XML:

### Available Filter Types

1. **Text Input Filter**
```xml
<item name="customer_email" xsi:type="array">
    <item name="label" xsi:type="string">Email</item>
    <item name="element" xsi:type="string">text</item>
</item>
```
- Default filter type
- Supports partial matching
- Auto-applies on input change
- Supports Enter key for immediate filtering

2. **Select Filter**
```xml
<item name="status" xsi:type="array">
    <item name="label" xsi:type="string">Status</item>
    <item name="element" xsi:type="string">select</item>
    <item name="source_model" xsi:type="string">Your\Module\Model\Source\Status</item>
</item>
```
- Single-value selection
- Dropdown interface
- Supports custom source models
- "Any" option included by default

3. **Multiselect Filter**
```xml
<item name="store_id" xsi:type="array">
    <item name="label" xsi:type="string">Store</item>
    <item name="element" xsi:type="string">multiselect</item>
    <item name="source_model" xsi:type="string">Your\Module\Model\Source\Store</item>
</item>
```
- Multiple value selection
- Searchable dropdown
- Supports Ctrl/Cmd for multiple selections
- "Any" option for clearing selection
- Built-in search functionality
- Maintains selected state in URL

### Filter Features

- **Caching**: 
  - Filter values are cached for better performance
  - Cache is applied for results > 100 items
  - 24-hour cache lifetime for filter options
  - Cache is tagged for easy management

- **URL Integration**:
  - Filter states are maintained in URL parameters
  - Supports browser back/forward navigation
  - Bookmarkable filtered states
  - Clear all filters functionality

- **UI/UX Features**:
  - Responsive design
  - Mobile-friendly dropdowns
  - Clear visual feedback
  - Consistent styling with Magento admin
  - Tooltip hints for usage

### Example Configuration

Full example of filter configuration in layout XML:
```xml
<argument name="fields" xsi:type="array">
    <!-- Text Filter -->
    <item name="increment_id" xsi:type="array">
        <item name="label" xsi:type="string">Order #</item>
        <item name="element" xsi:type="string">text</item>
    </item>
    
    <!-- Select Filter -->
    <item name="status" xsi:type="array">
        <item name="label" xsi:type="string">Status</item>
        <item name="element" xsi:type="string">select</item>
        <item name="source_model" xsi:type="string">Magento\Sales\Model\Order\Config</item>
    </item>
    
    <!-- Multiselect Filter -->
    <item name="store_id" xsi:type="array">
        <item name="label" xsi:type="string">Store View</item>
        <item name="element" xsi:type="string">multiselect</item>
        <item name="source_model" xsi:type="string">Magento\Store\Model\System\Store</item>
    </item>
</argument>
```

### Custom Source Model

Example of a custom source model for filters:
```php
namespace Your\Module\Model\Source;

use Mage\Grid\Model\Fields\DataSourceInterface;
use Magento\Framework\App\ResourceConnection;

class CustomSource implements DataSourceInterface
{
    protected $resource;
    
    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
    }

    public function getValues($field)
    {
        // Your custom logic to fetch values
        return ['value1', 'value2', 'value3'];
    }
}
```

### JavaScript Events

The grid filters emit several events that you can listen to:
```javascript
// Filter change event
document.addEventListener('grid:filter:change', function(event) {
    console.log('Filter changed:', event.detail);
});

// Filter clear event
document.addEventListener('grid:filter:clear', function(event) {
    console.log('Filters cleared');
});
```

## Replacing GridJS with Alternative Grid Systems

The Mage Grid module is designed to be flexible and allows you to replace GridJS with any other grid system. Here's how to implement alternative grid solutions:

### 1. DataTables Integration

To use DataTables instead of GridJS:

1. **Create a new template** (`view/adminhtml/templates/grid/datatable-component.phtml`):
```php
<?php
// Get grid data
$fields = array_keys($block->getFieldsNames());
$fieldsFull = $block->getFields();
$jsonGridData = $block->getGridJsonData();
$fieldsConfig = $block->getFieldsConfig();
$tableName = $block->getTableName();
$fieldsNames = $block->getFieldsNames();
$filters = $this->getRequest()->getParam('filter', []);
$processedFields = $block->getProcessedFields($fields, $fieldsConfig, $filters);
?>

<div id="grid-wrapper">
    <!-- Render filters -->
    <?= $block->getFiltersHtml(['fields' => $processedFields, 'filters' => $filters]) ?>

    <!-- DataTables container -->
    <table id="grid-table" class="display">
        <thead>
            <tr>
                <?php foreach ($fieldsNames as $field => $label): ?>
                    <th><?= $label ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
    </table>
</div>

<script>
require(['jquery', 'datatables'], function($) {
    $('#grid-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: window.location.href,
            data: function(d) {
                d.data = true;
                return d;
            }
        },
        columns: <?= json_encode(array_map(function($field) use ($fieldsNames) {
            return ['data' => $field, 'title' => $fieldsNames[$field]];
        }, $fields)) ?>,
        pageLength: 20,
        order: [[0, 'desc']]
    });
});
</script>
```

2. **Update your layout XML** to use the new template:
```xml
<block class="Mage\Grid\Block\GenericGrid" 
       name="grid_generic_grid" 
       template="Mage_Grid::grid/datatable-component.phtml">
    <!-- ... existing arguments ... -->
</block>
```

### 2. Simple HTML Table

For a basic HTML table without JavaScript:

1. **Create a new template** (`view/adminhtml/templates/grid/simple-table.phtml`):
```php
<?php
$fields = array_keys($block->getFieldsNames());
$fieldsFull = $block->getFields();
$data = $block->getViewModel()->getGridData();
$fieldsConfig = $block->getFieldsConfig();
$fieldsNames = $block->getFieldsNames();
$filters = $this->getRequest()->getParam('filter', []);
$processedFields = $block->getProcessedFields($fields, $fieldsConfig, $filters);
?>

<div id="grid-wrapper">
    <!-- Render filters -->
    <?= $block->getFiltersHtml(['fields' => $processedFields, 'filters' => $filters]) ?>

    <!-- Simple HTML table -->
    <table class="data-grid">
        <thead>
            <tr>
                <?php foreach ($fieldsNames as $field => $label): ?>
                    <th><?= $label ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($fields as $field): ?>
                        <td><?= $block->getViewModel()->processField($field, $row[$field], $row) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Simple pagination -->
    <?php if ($block->getViewModel()->getTotalPages() > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $block->getViewModel()->getTotalPages(); $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="<?= $i == $block->getViewModel()->getCurrentPage() ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
```

### 3. Custom Grid System

To implement your own grid system:

1. **Create a new template** with your preferred grid library
2. **Use the block's methods** to get data and configuration:
   - `getFieldsNames()`: Get field labels
   - `getGridJsonData()`: Get grid data as JSON
   - `getFieldsConfig()`: Get field configurations
   - `getProcessedFields()`: Get processed field data
   - `getFiltersHtml()`: Get rendered filters

3. **Implement your own JavaScript** to handle:
   - Data loading
   - Pagination
   - Sorting
   - Filtering

### Key Components to Override

When replacing GridJS, you mainly need to focus on:

1. **Template File**: Create your own `grid-component.phtml`
2. **JavaScript**: Implement your grid initialization
3. **CSS**: Add your grid styling
4. **Data Processing**: Use the existing processor system

The rest of the module (data loading, filtering, processing) remains unchanged.

### Example: Using AG Grid

```php
<!-- view/adminhtml/templates/grid/ag-grid-component.phtml -->
<div id="grid-wrapper">
    <?= $block->getFiltersHtml($filterData) ?>
    <div id="myGrid" style="height: 500px; width: 100%;"></div>
</div>

<script>
require(['ag-grid-community'], function(agGrid) {
    const columnDefs = <?= json_encode(array_map(function($field) use ($fieldsNames) {
        return {
            field: $field,
            headerName: $fieldsNames[$field]
        };
    }, $fields)) ?>;

    const gridOptions = {
        columnDefs: columnDefs,
        rowData: <?= $jsonGridData ?>,
        pagination: true,
        paginationPageSize: 20,
        onGridReady: params => {
            params.api.sizeColumnsToFit();
        }
    };

    new agGrid.Grid(document.querySelector('#myGrid'), gridOptions);
});
</script>
```

### Best Practices

1. **Keep Data Processing**: Use the existing processor system for consistent data formatting
2. **Maintain Filter Integration**: Keep the filter system for consistent filtering
3. **Preserve Field Configuration**: Use the existing field configuration system
4. **Handle Server-Side Operations**: Implement proper server-side pagination and filtering
5. **Maintain Responsiveness**: Ensure your grid works well on all devices

## Performance Monitoring & Optimization

The module includes built-in performance metrics to help you monitor and optimize your grid's performance:

### Performance Metrics Panel

The grid displays real-time performance metrics including:
- Server SQL Query time
- Server Count time
- AJAX Response time
- Total processing time

These metrics are displayed in the performance panel below the grid and are color-coded:
- Green: Normal performance
- Red: Performance issues detected (>1 second)

### Performance Optimization

If you notice performance issues (red indicators), here are some steps to optimize:

1. **Query Optimization**
   - Review your SQL queries using the Magento profiler
   - Add appropriate indexes to frequently filtered columns
   - Consider implementing query caching

2. **Data Loading**
   - Implement server-side pagination
   - Use lazy loading for large datasets
   - Consider implementing data caching

3. **Grid Configuration**
   - Reduce the number of columns if possible
   - Use efficient column formatters
   - Implement proper filtering strategies

### AI-Powered Optimization

For advanced optimization, you can ask our AI assistant:
```
"Analyze my grid performance and suggest optimizations for [specific issue]"
```

The AI will:
- Analyze your current configuration
- Review performance metrics
- Suggest specific optimizations
- Provide code examples for implementation

AI will provide specific recommendations based on your metrics


**Remember: Regular performance monitoring and optimization is key to maintaining a responsive and efficient grid system.**

## API Endpoints

The module provides REST API endpoints for accessing grid data programmatically:

### Available Endpoints

1. **Get Grid Data**
   ```
   GET /V1/grid/:gridId/data
   ```
   Parameters:
   - `gridId`: Identifier of the grid
   - `filters`: Array of filter criteria (optional)
   - `page`: Page number (default: 1)
   - `pageSize`: Items per page (default: 20)

   Response:
   ```json
   {
     "data": [
       {
         "id": "1",
         "name": "Example",
         "created_at": "2024-01-01 12:00:00"
       }
     ],
     "total_count": 100,
     "performance_metrics": {
       "execution_time": 150,
       "sql_time": 100,
       "count_time": 50
     }
   }
   ```

2. **Get Grid Configuration**
   ```
   GET /V1/grid/:gridId/config
   ```
   Returns the grid's configuration including:
   - Column definitions
   - Default settings
   - Available filters

3. **Get Grid Fields**
   ```
   GET /V1/grid/:gridId/fields
   ```
   Returns the list of available fields and their configurations.

4. **Get Grid Filters**
   ```
   GET /V1/grid/:gridId/filters
   ```
   Returns the available filter options for the grid.

### Authentication

All API endpoints require authentication using Magento's standard authentication methods:
- OAuth 2.0
- Integration tokens
- Admin tokens

### Example Usage

```php
// Using PHP
$client = new \Magento\Framework\HTTP\Client\Curl();
$client->addHeader('Authorization', 'Bearer ' . $token);
$client->get('https://your-store.com/rest/V1/grid/orders/data?page=1&pageSize=20');

// Using JavaScript
fetch('/rest/V1/grid/orders/data?page=1&pageSize=20', {
    headers: {
        'Authorization': 'Bearer ' + token
    }
})
.then(response => response.json())
.then(data => console.log(data));
```

### Performance Monitoring

The API response includes performance metrics to help monitor and optimize grid performance:
- `execution_time`: Total time taken to process the request
- `sql_time`: Time taken for SQL queries
- `count_time`: Time taken for count operations

### Error Handling

The API uses standard HTTP status codes and returns detailed error messages:
```json
{
    "message": "Error retrieving grid data: [specific error]",
    "trace": "Stack trace for debugging"
}
```

### Rate Limiting

To protect the server, API requests are rate-limited:
- 100 requests per minute per IP
- 1000 requests per hour per user

### Best Practices

1. **Caching**
   - Cache responses when possible
   - Use ETags for conditional requests
   - Implement client-side caching

2. **Pagination**
   - Always use pagination for large datasets
   - Keep page size reasonable (20-50 items)
   - Use cursor-based pagination for large datasets

3. **Filtering**
   - Use specific filters to reduce data size
   - Combine multiple filters when needed
   - Cache common filter combinations

4. **Error Handling**
   - Implement proper error handling
   - Use exponential backoff for retries
   - Log errors for monitoring

