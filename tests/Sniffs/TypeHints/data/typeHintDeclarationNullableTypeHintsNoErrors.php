<?php

namespace FooNamespace;

abstract class FooClass
{

	abstract public function withNullableReturnTypeHint(): ?string;

	public function withNullableParameterTypeHint(?string $a): void
	{

	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingReturnTypeHint
	 * @return string|null
	 */
	public function withSuppress()
	{
		return '';
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.UselessDocComment
	 * @param int|null $a
	 */
	public function withNullableParameterTypeHintAndSuppressedUselessDocComment(?int $a): void
	{
	}

	/**
	 * @return string[]|null
	 */
	public function returnsNullableArrayOfStrings(): ?array
	{
		return [];
	}

	/**
	 * @return static|null
	 */
	public function returnsNullableStaticAsSelf(): ?self
	{
		return null;
	}

	/**
	 * @return resource|null
	 */
	public function returnsNullableResource()
	{
		return null;
	}

	/**
	 * @return mixed|null
	 */
	public function returnsNullableMixed()
	{
		return null;
	}

	/**
	 * @param resource|null $a
	 */
	public function nullableResource($a): void
	{

	}

	/**
	 * @param mixed|null $a
	 */
	public function nullableMixed($a): void
	{

	}

	/**
	 * @param static|null $a
	 */
	public function nullableStaticAsSelf(?self $a): void
	{

	}

	/**
	 * @return \SomethingElse|null
	 */
	public function withMoreSpecificAnnotation(): ?\Something
	{
		return new \Something();
	}

	/**
	 * @param \SomethingElse|null $a
	 */
	public function withMoreSpecificParameterAnnotation(?\Something $a): void
	{
	}

	/**
	 * @param null|string $string
	 * @param int $int
	 * @param bool|null $bool
	 * @param float $float
	 * @param callable|null $callable
	 * @param mixed[] $array
	 * @param \FooNamespace\FooClass|null $object
	 * @return mixed
	 */
	abstract public function parametersWithWeirdDefinition(?string$string,int$int,?bool$bool=true,float$float,?callable$callable,array$array=[],?\FooNamespace\FooClass$object);

}
