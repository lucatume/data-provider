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

	public static function merge( array $baseArray ) {
		return self::initializeWith( $baseArray, false );
	}

	public static function wrapAndMerge( $baseArray ) {
		return self::initializeWith( $baseArray, true );
	}

	/**
	 * @param array $baseArray
	 *
	 * @return tad_TestDataProvider
	 */
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

	public function appending( $valueOrArray ) {
		$this->appendOrPrependValueOrArray( $valueOrArray, true );

		return $this;
	}

	public function prepending( $valueOrArray ) {
		$this->appendOrPrependValueOrArray( $valueOrArray, false );

		return $this;
	}

	public function with( array $array ) {
		$newOut = array();

		try {
			for ( $i = 0; $i < count( $this->out ); $i ++ ) {
				$arr      = array_merge_recursive( $this->out[ $i ], $array[ $i ] );
				$newOut[] = $arr;
			}
		} catch ( Exception $e ) {
			throw new InvalidArgumentException( 'Array to merge must be an array of arrays as the ones returned from PHPUnit data provider methods.', 1 );
		}
		$this->out = $newOut;

		return $this;
	}

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