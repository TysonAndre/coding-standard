<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\ThisTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use function array_merge;
use function preg_replace;
use function substr;

/**
 * @internal
 */
class AnnotationTypeHelper
{

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\TypeNode $typeNode
	 * @return \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode[]|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode[]
	 */
	public static function getIdentifierTypeNodes(TypeNode $typeNode): array
	{
		if ($typeNode instanceof ArrayTypeNode) {
			return self::getIdentifierTypeNodes($typeNode->type);
		}

		if (
			$typeNode instanceof UnionTypeNode
			|| $typeNode instanceof IntersectionTypeNode
		) {
			$identifierTypeNodes = [];
			foreach ($typeNode->types as $innerTypeNode) {
				$identifierTypeNodes = array_merge($identifierTypeNodes, self::getIdentifierTypeNodes($innerTypeNode));
			}
			return $identifierTypeNodes;
		}

		if ($typeNode instanceof GenericTypeNode) {
			$identifierTypeNodes = self::getIdentifierTypeNodes($typeNode->type);
			foreach ($typeNode->genericTypes as $innerTypeNode) {
				$identifierTypeNodes = array_merge($identifierTypeNodes, self::getIdentifierTypeNodes($innerTypeNode));
			}
			return $identifierTypeNodes;
		}

		if ($typeNode instanceof NullableTypeNode) {
			return self::getIdentifierTypeNodes($typeNode->type);
		}

		/** @var \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $typeNode */
		$typeNode = $typeNode;
		return [$typeNode];
	}

	/**
	 * @param \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode|\PHPStan\PhpDocParser\Ast\Type\ThisTypeNode $typeNode
	 * @return string
	 */
	public static function getTypeHintFromNode(TypeNode $typeNode): string
	{
		return $typeNode instanceof ThisTypeNode
			? (string) $typeNode
			: $typeNode->name;
	}

	public static function export(TypeNode $typeNode): string
	{
		$exportedTypeNode = preg_replace(['~\\s*(&|\|)\\s*~'], '\\1', (string) $typeNode);

		if (
			$typeNode instanceof UnionTypeNode
			|| $typeNode instanceof IntersectionTypeNode
		) {
			$exportedTypeNode = substr($exportedTypeNode, 1, -1);
		}

		return $exportedTypeNode;
	}

	public static function change(TypeNode $masterTypeNode, TypeNode $typeNodeToChange, TypeNode $changedTypeNode): TypeNode
	{
		if ($masterTypeNode === $typeNodeToChange) {
			return $changedTypeNode;
		}

		if ($masterTypeNode instanceof UnionTypeNode) {
			$types = [];
			foreach ($masterTypeNode->types as $typeNone) {
				$types[] = self::change($typeNone, $typeNodeToChange, $changedTypeNode);
			}

			return new UnionTypeNode($types);
		}

		if ($masterTypeNode instanceof IntersectionTypeNode) {
			$types = [];
			foreach ($masterTypeNode->types as $typeNone) {
				$types[] = self::change($typeNone, $typeNodeToChange, $changedTypeNode);
			}

			return new IntersectionTypeNode($types);
		}

		if ($masterTypeNode instanceof GenericTypeNode) {
			$genericTypes = [];
			foreach ($masterTypeNode->genericTypes as $genericTypeNode) {
				$genericTypes[] = self::change($genericTypeNode, $typeNodeToChange, $changedTypeNode);
			}

			/** @var \PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode $identificatorTypeNode */
			$identificatorTypeNode = self::change($masterTypeNode->type, $typeNodeToChange, $changedTypeNode);
			return new GenericTypeNode($identificatorTypeNode, $genericTypes);
		}

		if ($masterTypeNode instanceof ArrayTypeNode) {
			return new ArrayTypeNode(self::change($masterTypeNode->type, $typeNodeToChange, $changedTypeNode));
		}

		if ($masterTypeNode instanceof NullableTypeNode) {
			return new NullableTypeNode(self::change($masterTypeNode->type, $typeNodeToChange, $changedTypeNode));
		}

		return clone $masterTypeNode;
	}

}
