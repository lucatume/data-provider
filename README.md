# Data Providers
Easier data providers for PHPUnit

## Installation
Use [Composer](https://getcomposer.org/) to install the package the easy way using

    composer require lucatume/data-provider --dev

or edit <code>composer.json</code> file

    "require-dev": {
        "lucatume/data-provider": "dev-master"
    }

Or go the slow, unreliable, error-prone way of manual installation downloading the package, unzipping it somewhere and manually including the file like

    require /some/path/PHPUnitDataProvider.php

##PHPUnit data provider format

[PHPUnit](https://phpunit.de/) expects methods providing data, "data providers", to return an array of arrays each containing the values that will be passed to the test method; I will hence say "PHPUnit data provider format" to refer arrays with a structure like

    [
        [ value1, value2, value3, ...]
        [ value1, value2, value3, ...]
        [ value1, value2, value3, ...]
        [ value1, value2, value3, ...]
        [ value1, value2, value3, ...]
    ]

## Methods
Once the package is safely in place it can be used calling its static access methods:

* <code>merge</code> - will merge a PHPUnit data provider format array with other PHPUnit data provider format arrays, scalar values or strings.
* <code>wrap</code> - will first generate a PHPUnit data provider format array wrapping the elements of the provided array and then merging it with other PHPUnit data provider format arrays, scalar values or strings.

After the first step the class provides chainable methods to create data provider arrays in a fluent format:

* <code>prepend</code> - prepends a scalar value or a string to any array in the PHPUnit data provider format array
* <code>append</code> - appends a scalar value or a string to any array in the PHPUnit data provider format array
* <code>with</code> - merges the given PHPunit data provider format array with another PHPUnit data provider format array, the merging happens as per the <code>array_merge</code> function where the array parameter of the <code>with</code> method is "appended" to the first

>Note that the cardinality of the returned array will be the same as the one of the initial array and that arrays merged with the initial array must have a cardinality equal or superior to the initial array.

The method <code>provide</code> will return the merged array.

## Examples
I want to test the <code>isOdd</code> method, usually I would write

    /**
     * @dataProvider valuesAndOddResponses
     */
    public function testIsOdd($value, $response){
        $sut = new SomeClass();
        $this->assertEquals($response, $sut->isOdd($value));
    }

    public function valuesAndOddResponses(){
        return array(
            array(1, true),
            array(2, false),
            array(3, true),
            array(4, false),
            array(43, true),
            array(-1, true),
            array(-4, false),
            array(-1.0, true),
            array(5.0, true),
            array(8.0, false)
            );
    }

    /**
     * @dataProvider valuesAndEvenResponses
     */
    public function testIsOdd($value, $response){
        $sut = new SomeClass();
        $this->assertEquals($response, $sut->isEven($value));
    }

    public function valuesAndEvenResponses(){
        return array(
            array(1, false),
            array(2, true),
            array(3, false),
            array(4, true),
            array(43, false),
            array(-1, false),
            array(-4, true),
            array(-1.0, false),
            array(5.0, false),
            array(8.0, true)
            );
    }

using PHPUnitDataProvider I could re-use smaller data providers like

    private oddValues = array(1, 3, 43, -1, -1.0, 5.0);
    private evenValues = array(2, 4, 8.0, -4);
    
    public function valuesAndOddResponses(){
        return array_merge(
            PHPUnitDataProvider::wrap($this->oddValues)
            ->append(true)->provide(),
            PHPUnitDataProvider::wrap($this->evenValues)
            ->append(false)->provide()
            );
    }

    public function valuesAndEvenResponses(){
    return array_merge(
        PHPUnitDataProvider::wrap($this->oddValues)
        ->append(false)->provide(),
        PHPUnitDataProvider::wrap($this->evenValues)
        ->append(true)->provide()
        );
    }

while this might be an overhead on smaller projects it allow for easier test code maintenance.

## Changelog
* <code>0.1.0</code> - initial commit