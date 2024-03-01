# Magento 2 GridJS
**GridJS** and **DataTable** integration with Magento 2

Grid.js(https://gridjs.io/) is a Free and open-source JavaScript table plugin. It works with most JavaScript frameworks, including React, Angular, Vue and VanillaJs.

Grid.js uses Preact to render the elements and that means that you can take advantage of Preact's Virtual DOM and render complex cells.

**DataTable** integration with magento 

**DataTables** (https://datatables.net/) is a Javascript HTML table-enhancing library. It is a highly flexible tool, built upon the foundations of progressive enhancement, that adds all of these advanced features to any HTML table.

At First, GridJS was integrated, but after DataTable was added. 

All extensions come as jQuery plugins but can work independently. 

I can also have **AG Grid** integration for Magento 2 but it is not part of this open source solution.
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
After installation examples will be embedded at the bottom of the admin dashboard 

![image](https://github.com/Genaker/magento_gridjs/assets/9213670/769b0781-802f-44ba-a556-4fb511937cd3)

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
