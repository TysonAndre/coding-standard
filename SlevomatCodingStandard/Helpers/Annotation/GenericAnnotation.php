<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

/**
 * @internal
 */
class GenericAnnotation extends Annotation
{

	public function isInvalid(): bool
	{
		return false;
	}

}
