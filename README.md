EzMatrixBundle
==============

Support for matrix in eZ Publish 5.x

FieldDefinition
---------------

It allows to add the legacy know as ezmatrix datatype to a eZ5 ContentType using the new API.

For that you need to pass an array of columns as fieldSettings of the fieldDefinition.
You may also pass a `defaultNRows`value to specifiy the default number of rows your matrix will have.

Columns needs to be an array of associative arrays having id and label as keys provided in the order you
want the columns appear in the matrix


Example
```php
<?php
$matrixField = new FieldDefinitionCreateStruct(
    array(
        'fieldTypeIdentifier' => 'ezmatrix',
        'identifier' => 'matrix',
        'names' => array( 'esl-ES' => 'Matriz de Enlaces' ),
        'position' => 2,
        'isRequired' => true,
        'isSearchable' => false,
        'fieldSettings' => array(
            'columns' => array(
                 array(
                     'id' => 'col_1',
                     'label' => 'Label for Column 1'
                 ),
                 array(
                    'id' => 'col_2',
                    'label' => 'Label for Column 2'
                 ),
            ),
            'defaultNRows' => 10
        )
    )
);

```


