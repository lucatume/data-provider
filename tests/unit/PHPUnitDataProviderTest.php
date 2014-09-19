<?php

use lucatume\DataProvider\PHPUnitDataProvider;

class PHPUnitDataProviderTest extends \PHPUnit_Framework_TestCase {

	protected function setUp() {
	}

	protected function tearDown() {
	}

	public function valuesNotArray() {
		return array(
			array( 'string' ),
			array( 23 ),
			array( 23.23 ),
			array( '' ),
			array( null ),
			array( new stdClass() ) );
	}

	/**
	 * @test
	 * it should allow appending data providing methods with values
	 * @dataProvider valuesNotArray
	 */
	public function it_should_allow_appending_data_providing_methods_with_values( $value ) {
		$in       = array( array( 'one' ), array( 'two' ), array( 'three' ) );
		$expected = array( array( 'one', $value ), array( 'two', $value ), array( 'three', $value ) );
		$out      = PHPUnitDataProvider::merge( $in )->appending( $value )->andReturn();
		$this->assertEquals( $expected, $out );
	}

	public function threeValues() {
		return array(
			array( 'one', 'two', 'three' ),
			array( '', 23, null ),
			array( 'one', new stdClass(), 23 ),
			array( 12, 21, 18 ),
			array( null, null, null ),
			array( false, false, true ),
			array( true, null, 23 ),
			array( 'one', null, 'three' ), );
	}

	/**
	 * @test
	 * it should allow appending data providing methods with multiple values
	 * @dataProvider threeValues
	 */
	public function it_should_allow_appending_data_providing_methods_with_multiple_values( $value1, $value2, $value3 ) {
		$in       = array( array( 'one' ), array( 'two' ), array( 'three' ) );
		$expected = array(
			array( 'one', $value1, $value2, $value3 ),
			array( 'two', $value1, $value2, $value3 ),
			array( 'three', $value1, $value2, $value3 ) );
		$out      = PHPUnitDataProvider::merge( $in )->appending( $value1 )->appending( $value2 )->appending( $value3 )
		                               ->andReturn();
		$this->assertEquals( $expected, $out );
	}

	/**
	 * @test
	 * it should allow appending data with an array in PHPUnit data provider format
	 */
	public function it_should_allow_appending_data_with_an_array_in_php_unit_data_provider_format() {
		$in       = array( array( 'one' ), array( 'two' ), array( 'three' ) );
		$expected = array(
			array( 'one', 'one', 'two', 'three' ),
			array( 'two', '', 23, null ),
			array( 'three', 'one', new stdClass(), 23 ) );
		$out      = PHPUnitDataProvider::merge( $in )->with( $this->threeValues() )->andReturn();
		$this->assertEquals( $expected, $out );
	}

	/**
	 * @test
	 * it should allow appending data with multiple arrays in PHPUnit data provider fomrmat
	 */
	public function it_should_allow_appending_data_with_multiple_arrays_in_php_unit_data_provider_fomrmat() {
		$in       = array( array( 'one' ), array( 'two' ), array( 'three' ) );
		$expected = array(
			array( 'one', 'one', 'two', 'three', 'string' ),
			array( 'two', '', 23, null, 23 ),
			array( 'three', 'one', new stdClass(), 23, 23.23 ) );
		$out      = PHPUnitDataProvider::merge( $in )->with( $this->threeValues() )->with( $this->valuesNotArray() )
		                               ->andReturn();
		$this->assertEquals( $expected, $out );
	}

	/**
	 * @test
	 * it should allow appending data with arrays in PHPUnit data provider method format and values
	 * @dataProvider valuesNotArray
	 */
	public function it_should_allow_appending_data_with_arrays_in_php_unit_data_provider_method_format_and_values( $value ) {
		$in       = array( array( 'one' ), array( 'two' ), array( 'three' ) );
		$expected = array(
			array( 'one', 'one', 'two', 'three', $value ),
			array( 'two', '', 23, null, $value ),
			array( 'three', 'one', new stdClass(), 23, $value ) );
		$out      = PHPUnitDataProvider::merge( $in )->with( $this->threeValues() )->appending( $value )->andReturn();
		$this->assertEquals( $expected, $out );
	}

	/**
	 * @test
	 * it should allow appending data with a single array
	 */
	public function it_should_allow_appending_data_with_a_single_array() {
		$in       = array( array( 'one' ), array( 'two' ), array( 'three' ) );
		$expected = array(
			array( 'one', 'one', 'two', 'three', 23, 46 ),
			array( 'two', '', 23, null, 23, 46 ),
			array( 'three', 'one', new stdClass(), 23, 23, 46 ) );
		$out      = PHPUnitDataProvider::merge( $in )->with( $this->threeValues() )->appending( array( 23, 46 ) )
		                               ->andReturn();
		$this->assertEquals( $expected, $out );
	}

	/**
	 * @test
	 * it should allow prepending values to a PHPUnit data provider format array
	 */
	public function it_should_allow_prepending_values_to_a_php_unit_data_provider_format_array() {
		$in       = array( array( 'one' ), array( 'two' ), array( 'three' ) );
		$expected = array(
			array( 23, 46, 'one', 'one', 'two', 'three' ),
			array( 23, 46, 'two', '', 23, null ),
			array( 23, 46, 'three', 'one', new stdClass(), 23 ) );
		$out      = PHPUnitDataProvider::merge( $in )->with( $this->threeValues() )->prepending( 46 )->prepending( 23 )
		                               ->andReturn();
		$this->assertEquals( $expected, $out );
	}

	/**
	 * @test
	 * it should allow wrapping array element in arrays
	 */
	public function it_should_allow_wrapping_array_element_in_arrays() {
		$in       = array( 'one', 'two', 'three' );
		$expected = array(
			array( 23, 46, 'one', 'one', 'two', 'three' ),
			array( 23, 46, 'two', '', 23, null ),
			array( 23, 46, 'three', 'one', new stdClass(), 23 ) );
		$out      = PHPUnitDataProvider::wrapAndMerge( $in )->with( $this->threeValues() )->prepending( 46 )
		                               ->prepending( 23 )->andReturn();
		$this->assertEquals( $expected, $out );
	}

	/**
	 * @test
	 * it should return wrapper array if not merging appending or prepending anything
	 */
	public function it_should_return_wrapper_array_if_not_merging_appending_or_prepending_anything() {
		$in       = array( 'one', 'two', 23 );
		$expected = array( array( 'one' ), array( 'two' ), array( 23 ) );
		$this->assertEquals( $expected, PHPUnitDataProvider::wrapAndMerge( $in )->andReturn() );
	}

	/**
	 * @test
	 * it should return PHPUnit data provided format array if not merging prepending and appending anything
	 */
	public function it_should_return_php_unit_data_provided_format_array_if_not_merging_prepending_and_appending_anything() {
		$this->assertEquals( $this->threeValues(), PHPUnitDataProvider::merge( $this->threeValues() )->andReturn() );
	}

	/**
	 * @test
	 * it should allow array merging
	 */
	public function it_should_allow_array_merging() {
		$one      = array( 1, 2, 3 );
		$two      = array( 4, 5, 6 );
		$expected = array( array( 1 ), array( 2 ), array( 3 ), array( 4 ), array( 5 ), array( 6 ) );
		$out      = array_merge( PHPUnitDataProvider::wrapAndMerge( $one )
		                                            ->andReturn(), PHPUnitDataProvider::wrapAndMerge( $two )
		                                                                              ->andReturn() );
		$this->assertEquals( $expected, $out );
	}

	/**
	 * @test
	 * it should allow merging of PHPUnit data provider format arrays
	 */
	public function it_should_allow_merging_of_php_unit_data_provider_format_arrays() {
		$one      = array( array( 1 ), array( 2 ) );
		$two      = array( array( 3 ), array( 4 ) );
		$expected = array( array( 1 ), array( 2 ), array( 3 ), array( 4 ) );
		$out      = array_merge( PHPUnitDataProvider::merge( $one )->andReturn(), PHPUnitDataProvider::merge( $two )
		                                                                                             ->andReturn() );
		$this->assertEquals( $expected, $out );
	}
}