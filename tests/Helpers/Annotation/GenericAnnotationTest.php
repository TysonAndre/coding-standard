<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use SlevomatCodingStandard\Helpers\TestCase;

class GenericAnnotationTest extends TestCase
{

	public function testAnnotation(): void
	{
		$annotation = new GenericAnnotation('@see', 1, 10, null, 'Whatever');

		self::assertSame('@see', $annotation->getName());
		self::assertSame(1, $annotation->getStartPointer());
		self::assertSame(10, $annotation->getEndPointer());
		self::assertNull($annotation->getParameters());
		self::assertSame('Whatever', $annotation->getContent());

		self::assertFalse($annotation->isInvalid());
	}

}
