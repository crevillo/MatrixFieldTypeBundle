EzMatrixBundle
==============

Support for matrix in eZ Publish 5.x

Provide FieldDefinition
-----------------------

To add an attribute of this FieldType to any of your ContentTypes you need  to pass an array of columns as fieldSettings
of the FieldDefinition. You may also pass a `defaultNRows`value to specifiy the default number of rows your matrix will
have. If this value is not provided, 1 will be taken as default

Columns needs to be an array of associative arrays having id and label as keys provided in the order you
want the columns appear in the matrix.


Example
```php
<?php
$matrixField = new FieldDefinitionCreateStruct(
    array(
        'fieldTypeIdentifier' => 'ezmatrix',
        'identifier' => 'matrix',
        'names' => array( 'eng-GB' => 'Matrix' ),
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

Provide Field Value
-------------------

In order to add value to Matrix Field you need to pass an array of associative arrays. These associative arrays will
consists in pairs of columns ids and values

Example
```php
<?php
$contentType = $contentTypeService->loadContentTypeByIdentifier( $contentTypeIdentifier );
$contentCreateStruct = $contentService->newContentCreateStruct( $contentType, 'eng-GB' );
$rows = array(
    // row 0
    array(
       'col_1' => 'Value for matrix[0][0]',
       'col_2' => 'Value for matrix[0][1]'
    ),
    // row 1
    array(
       'col_1' => 'Value for matrix[1][0]',
       'col_2' => 'Value for matrix[1][1]'
    )
)
contentCreateStruct->setField( 'matrix', $rows );
```

Validation
----------

A validation is performed when the field value is provided. It will compare the keys passed in the field value rows
with the ids of the columns defined in the FieldDefinition part.

If any of the keys of the rows are not present in the columns Ids, validation will fail and you won't be able
to create your Value.

Example, this will fail...
```php
rows = array(
    // row 0
    array(
       'col_1_1' => 'Value for matrix[0][0],
    ),
)
```



