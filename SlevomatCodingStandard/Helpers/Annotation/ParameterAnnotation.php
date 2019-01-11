<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use function sprintf;

/**
 * @internal
 */
class ParameterAnnotation extends Annotation
{

	/** @var \PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode|null */
	private $contentNode;

	public function __construct(
		string $name,
		int $startPointer,
		int $endPointer,
		?string $parameters,
		?string $content,
		?ParamTagValueNode $contentNode
	)
	{
		if ($name !== '@param') {
			throw new InvalidArgumentException(sprintf('Unsupported annotation %s.', $name));
		}

		parent::__construct($name, $startPointer, $endPointer, $parameters, $content);

		$this->contentNode = $contentNode;
	}

	public function isInvalid(): bool
	{
		return $this->contentNode === null;
	}

	public function hasDescription(): bool
	{
		return $this->getDescription() !== null;
	}

	public function getDescription(): ?string
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		return $this->contentNode->description !== '' ? $this->contentNode->description : null;
	}

	public function getParameterName(): ?string
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		return $this->contentNode->parameterName !== '' ? $this->contentNode->parameterName : null;
	}

	/**
	 * @return \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode
	 */
	public function getType(): TypeNode
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		/** @var \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode $type */
		$type = $this->contentNode->type;
		return $type;
	}

}
