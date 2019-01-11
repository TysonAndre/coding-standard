<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use SlevomatCodingStandard\Helpers\TestCase;

class ThrowsAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new ThrowsAnnotation('@throws', 1, 10, null, 'Description', new ThrowsTagValueNode(new IdentifierTypeNode('Exception'), 'Description'));

		self::assertSame('@throws', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertNull($annotation->getParameters());
		self::assertSame('Description', $annotation->getContent());

		self::assertFalse($annotation->isInvalid());
		self::assertTrue($annotation->hasDescription());
		self::assertSame('Description', $annotation->getDescription());
		self::assertInstanceOf(IdentifierTypeNode::class, $annotation->getType());
	}

	public function testUnsupportedAnnotation(): void
	{
		self::expectException(InvalidArgumentException::class);
		self::expectExceptionMessage('Unsupported annotation @param.');
		new ThrowsAnnotation('@param', 1, 1, null, null, null);
	}

	public function testGetDescriptionWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @throws annotation.');
		$annotation = new ThrowsAnnotation('@throws', 1, 1, null, null, null);
		$annotation->getDescription();
	}

	public function testGetTypeWhenInvalid(): void
	{
		self::expectException(LogicException::class);
		self::expectExceptionMessage('Invalid @throws annotation.');
		$annotation = new ThrowsAnnotation('@throws', 1, 1, null, null, null);
		$annotation->getType();
	}

}
