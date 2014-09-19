<?php

namespace lucatume\DataProvider;
/**
 * Class tad_TestDataProvider
 *
 * Provides data sets for PHPUnit tests.
 */
class PHPUnitDataProvider {

	/**
	 * @var array An array of arrays in the same format PHPUnit data
	 *            provider methods should return.
	 */
	protected $out;

	/**
	 * Returns values that will be type juggled to false.
	 *
	 * @return array An array of arrays as per the dataProvider PHPUnit format.
	 */
	public static function falsyValues() {
		return array(
			array( '' ),
			array( 0 ),
			array( null ),
			array( array() ),
			array( 0.0 ),
			array( '0' ),
			array( false ) );
	}

	/**
	 * Returns values that will be type juggled to true.
	 *
	 * @return array An array of arrays as per the dataProvider PHPUnit format.
	 */
	public static function truthyValues() {
		return array(
			array( true ),
			array( 'true' ),
			array( 1 ),
			array( 'string' ),
			array( array( 'some' ) ),
			array( array( 'some' => 'value' ) ),
			array( - 1 ) );
	}

	/**
	 * Merges PHPUnit data provider format arrays.
	 *
	 * @param array $baseArray
	 *
	 * @return lucatume\DataProvider\PHPUnitDataProvider
	 * @throws BadMethodCallException
	 */
	public static function merge( array $baseArray ) {
		return self::initializeWith( $baseArray, false );
	}

	/**
	 * Wraps elements of an array in arrays to make the array stick to
	 * PHPUnit data provider format.
	 *
	 * @param $baseArray
	 *
	 * @return lucatume\DataProvider\PHPUnitDataProvider
	 * @throws BadMethodCallException
	 */
	public static function wrap( $baseArray ) {
		return self::initializeWith( $baseArray, true );
	}

	protected static function initializeWith( array $baseArray, $wrap = false ) {
		$instance = new self();
		if ( $wrap ) {
			$wrappedBaseArray = array();
			foreach ( $baseArray as $value ) {
				$wrappedBaseArray[] = array( $value );
			}
			$baseArray = $wrappedBaseArray;
		}
		foreach ( $baseArray as $value ) {
			if ( ! is_array( $value ) ) {
				throw new BadMethodCallException( 'Base array must be an array of arrays as PHPUnit data provider methods should return', 1 );
			}
			if ( empty( $value ) ) {
				throw new BadMethodCallException( 'Base array must contain not empty arrays', 2 );
			}
		}
		$instance->out = $baseArray;

		return $instance;
	}

	/**
	 * Appends a value or the elements of an array to each array.
	 *
	 * @param $valueOrArray Either a single value or an array of values.
	 *
	 * @return $this The calling instance to allow for method
	 *               chaining.
	 */
	public function appending( $valueOrArray ) {
		$this->appendOrPrependValueOrArray( $valueOrArray, true );

		return $this;
	}

	/**
	 * Prepends a value or the elements of an array to each array.
	 *
	 * @param $valueOrArray Either a value or an array of values.
	 *
	 * @return $this The calling instance to allow for method
	 *               chaining.
	 */
	public function prepending( $valueOrArray ) {
		$this->appendOrPrependValueOrArray( $valueOrArray, false );

		return $this;
	}

	/**
	 * Merges elements of two PHPUnit data provider format arrays
	 * into one.
	 *
	 * @param array $array
	 *
	 * @return $this The calling instance to allow for method
	 *               chaining.
	 * @throws InvalidArgumentException
	 */
	public function with( array $array ) {
		$newOut = array();
		$plus = count($this->out) - count($array);
		if ($plus > 0) {
			throw new \InvalidArgumentException("Base array has $plus elements more than the array to merge with it: they should have the same number of elements.", 1);
		}
		try {
			for ( $i = 0; $i < count( $this->out ); $i ++ ) {
				$arr      = array_merge_recursive( $this->out[ $i ], $array[ $i ] );
				$newOut[] = $arr;
			}
		} catch ( Exception $e ) {
			throw new InvalidArgumentException( 'Array to merge must be an array of arrays as the ones returned from PHPUnit data provider methods.', 2);
		}
		$this->out = $newOut;

		return $this;
	}

	/**
	 * @return array The resulting merged array of arrays in
	 *               the PHPUnit data provider format.
	 */
	public function andReturn() {
		return $this->out;
	}

	/**
	 * @param $valueOrArray
	 * @param $appending
	 */
	protected function appendOrPrependValueOrArray( $valueOrArray, $appending ) {
		$newOut = array();
		if ( is_array( $valueOrArray ) ) {
			foreach ( $this->out as $arr ) {
				if ( $appending ) {
					$newOut[] = array_merge_recursive( $arr, $valueOrArray );
				} else {
					$newOut[] = array_merge_recursive( $valueOrArray, $arr );
				}
			}
		} else {
			foreach ( $this->out as $arr ) {
				if ( $appending ) {
					$arr[]    = $valueOrArray;
					$newOut[] = $arr;
				} else {
					$arr      = array_reverse( $arr );
					$arr[]    = $valueOrArray;
					$arr      = array_reverse( $arr );
					$newOut[] = $arr;
				}
			}
		}
		$this->out = $newOut;
	}
}