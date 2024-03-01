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
