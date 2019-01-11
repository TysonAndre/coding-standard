<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

use InvalidArgumentException;
use LogicException;
use PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use function sprintf;

/**
 * @internal
 */
class MethodAnnotation extends Annotation
{

	/** @var \PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueNode|null */
	private $contentNode;

	public function __construct(
		string $name,
		int $startPointer,
		int $endPointer,
		?string $parameters,
		?string $content,
		?MethodTagValueNode $contentNode
	)
	{
		if ($name !== '@method') {
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

	public function getMethodName(): ?string
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		return $this->contentNode->methodName !== '' ? $this->contentNode->methodName : null;
	}

	/**
	 * @return \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode|null
	 */
	public function getMethodReturnType(): ?TypeNode
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		/** @var \PHPStan\PhpDocParser\Ast\Type\UnionTypeNode|\PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode|\PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $type */
		$type = $this->contentNode->returnType;
		return $type;
	}

	/**
	 * @return \PHPStan\PhpDocParser\Ast\PhpDoc\MethodTagValueParameterNode[]
	 */
	public function getMethodParameters(): array
	{
		if ($this->isInvalid()) {
			throw new LogicException(sprintf('Invalid %s annotation.', $this->name));
		}

		return $this->contentNode->parameters;
	}

}
