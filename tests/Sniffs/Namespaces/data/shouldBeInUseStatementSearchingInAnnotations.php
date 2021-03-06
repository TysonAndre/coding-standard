<?php

namespace Foo\Test\Bla;

use Foo\Something;

/**
 * @method \DateTimeImmutable|int|\Foo\DateTime getProperty()
 * @method doSomething(\Foo\DateTime $date, $mixed)
 */
abstract class Bar
{

	/** @var \DateTimeImmutable|int|\Foo\DateTime */
	private $property;

	/**
	 * @var \Foo\DateTime
	 */
	private $property2;

	/**
	 * @ORM\Column \Foo\Anything
	 */
	public function noErrors()
	{
		/**@var*/
	}

	/**
	 * Description
	 *
	 * @param array|\Foo\ArrayObject|iterable $iterable Parameter description
	 * @param \Boo\Anything $anything
	 * @return \Foo\Something[] Return description
	 * @throws \Foo\Exception|SomeException Throws description
	 */
	public function method()
	{
		/** @var \Foo\Traversable|array $variable */

		/** @var BlaBla\Foo @variable2 */
	}

	/**
	 * @return \Foo\Something
	 */
	public function method2()
	{
	}

	/**
	 * @param \Foo\Something $something
	 * @return void
	 */
	abstract function abstractMethod(Something $something);

	/**
	 * @param $parameter
	 */
	public function invalidAnnotation($parameter)
	{

	}

}

/**
 * @property \DateTimeImmutable&\Foo\DateTime $property
 */
class IntersectionAndGeneric
{

	/** @var (\DateTimeImmutable|null|\Foo\DateTime)[] */
	public $a;

	/** @var \Foo\ArrayObject&\Traversable<int, \DateTimeImmutable> */
	public $b;

	/** @var string&(\DateTimeImmutable|\Foo\DateTime) */
	public $c;

	/** @var string|(\DateTimeImmutable&\Foo\DateTime) */
	public $d;

	/** @var \Foo\DateTime[][][] */
	public $e;

	/** @var (\DateTimeImmutable|\Foo\DateTime)[][][] */
	public $f;

	/** @var int|(\Foo\ArrayObject<string>&BlaBla\Foo)[] */
	public $g;

	/** @var \Foo\ArrayObject<\Foo\ArrayObject<\Foo\DateTime>> */
	public $h;

}
