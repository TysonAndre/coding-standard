<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\TestCase;

class ParameterAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new ParameterAnnotation('@param', 1, 10, null, 'Description', new ParamTagValueNode(new IdentifierTypeNode('string'), false, '$parameter', 'Description'));

		self::assertSame('@param', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertNull($annotation->getParameters());
		self::assertSame('Description', $annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertTrue($annotation->hasDescription());
		self::assertSame('Description', $annotation->getDescription());
		self::assertSame('$parameter', $annotation->getParameterName());
		self::assertInstanceOf(IdentifierTypeNode::class, $annotation->getType());
	}

	public function testUnsupportedAnnotation(): void
	{
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Unsupported annotation @var.');
		new ParameterAnnotation('@var', 1, 1, null, null, null);
	}

	public function testGetDescriptionWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @param annotation.');
		$annotation = new ParameterAnnotation('@param', 1, 1, null, null, null);
		$annotation->getDescription();
	}

	public function testGetParameterNameWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @param annotation.');
		$annotation = new ParameterAnnotation('@param', 1, 1, null, null, null);
		$annotation->getParameterName();
	}

	public function testGetTypeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @param annotation.');
		$annotation = new ParameterAnnotation('@param', 1, 1, null, null, null);
		$annotation->getType();
	}

}
