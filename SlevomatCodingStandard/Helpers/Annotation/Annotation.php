<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers\Annotation;

/**
 * @internal
 */
abstract class Annotation
{

	/** @var string */
	protected $name;

	/** @var int */
	protected $startPointer;

	/** @var int */
	protected $endPointer;

	/** @var string|null */
	protected $parameters;

	/** @var string|null */
	protected $content;

	public function __construct(
		string $name,
		int $startPointer,
		int $endPointer,
		?string $parameters,
		?string $content
	)
	{
		$this->name = $name;
		$this->startPointer = $startPointer;
		$this->endPointer = $endPointer;
		$this->parameters = $parameters;
		$this->content = $content;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getStartPointer(): int
	{
		return $this->startPointer;
	}

	public function getEndPointer(): int
	{
		return $this->endPointer;
	}

	public function getParameters(): ?string
	{
		return $this->parameters;
	}

	public function getContent(): ?string
	{
		return $this->content;
	}

	abstract public function isInvalid(): bool;

}
