<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\TestCase;

class MethodAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new MethodAnnotation('@method', 1, 10, null, 'string method() Description', new MethodTagValueNode(false, new IdentifierTypeNode('string'), 'method', [], 'Description'));

		self::assertSame('@method', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertNull($annotation->getParameters());
		self::assertSame('string method() Description', $annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertTrue($annotation->hasDescription());
		self::assertSame('Description', $annotation->getDescription());
		self::assertSame('method', $annotation->getMethodName());
		self::assertInstanceOf(IdentifierTypeNode::class, $annotation->getMethodReturnType());
		self::assertCount(0, $annotation->getMethodParameters());
	}

	public function testUnsupportedAnnotation(): void
	{
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Unsupported annotation @var.');
		new MethodAnnotation('@var', 1, 1, null, null, null);
	}

	public function testGetDescriptionWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @method annotation.');
		$annotation = new MethodAnnotation('@method', 1, 1, null, null, null);
		$annotation->getDescription();
	}

	public function testGetMethodNameWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @method annotation.');
		$annotation = new MethodAnnotation('@method', 1, 1, null, null, null);
		$annotation->getMethodName();
	}

	public function testGetMethodReturnTypeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @method annotation.');
		$annotation = new MethodAnnotation('@method', 1, 1, null, null, null);
		$annotation->getMethodReturnType();
	}

	public function testGetMethodParametersWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @method annotation.');
		$annotation = new MethodAnnotation('@method', 1, 1, null, null, null);
		$annotation->getMethodParameters();
	}

}
