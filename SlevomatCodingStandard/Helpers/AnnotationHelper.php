<?php declare(strict_types = 1);

namespace SlevomatCodingStandard\Helpers;

use PHP_CodeSniffer\Files\File;
use PHPStan\PhpDocParser\Ast\PhpDoc\InvalidTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use ReflectionMethod;
use SlevomatCodingStandard\Helpers\Annotation\Annotation;
use SlevomatCodingStandard\Helpers\Annotation\GenericAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\MethodAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\PropertyAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\ReturnAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\ThrowsAnnotation;
use SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation;
use const T_DOC_COMMENT_CLOSE_TAG;
use const T_DOC_COMMENT_STAR;
use const T_DOC_COMMENT_STRING;
use const T_DOC_COMMENT_TAG;
use const T_DOC_COMMENT_WHITESPACE;
use function array_key_exists;
use function in_array;
use function preg_match;
use function preg_replace_callback;
use function substr_count;
use function trim;

class AnnotationHelper
{

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param \SlevomatCodingStandard\Helpers\Annotation\VariableAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ParameterAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ReturnAnnotation|\SlevomatCodingStandard\Helpers\Annotation\ThrowsAnnotation|\SlevomatCodingStandard\Helpers\Annotation\PropertyAnnotation $annotation
	 * @param \PHPStan\PhpDocParser\Ast\Type\TypeNode $typeNode
	 * @param string $type
	 * @return string
	 */
	public static function fixAnnotationType(File $phpcsFile, Annotation $annotation, TypeNode $typeNode, string $type): string
	{
		$fixedAnnotationType = AnnotationTypeHelper::change($annotation->getType(), $typeNode, new IdentifierTypeNode($type));

		return preg_replace_callback(
			'~^(' . $annotation->getName() . '\\s+)(\\S+)~',
			function (array $matches) use ($fixedAnnotationType): string {
				return $matches[1] . AnnotationTypeHelper::export($fixedAnnotationType);
			},
			TokenHelper::getContent($phpcsFile, $annotation->getStartPointer(), $annotation->getEndPointer())
		);
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer
	 * @param string $annotationName
	 * @return \SlevomatCodingStandard\Helpers\Annotation\Annotation[]
	 */
	public static function getAnnotationsByName(File $phpcsFile, int $pointer, string $annotationName): array
	{
		$annotations = self::getAnnotations($phpcsFile, $pointer);

		if (!array_key_exists($annotationName, $annotations)) {
			return [];
		}

		return $annotations[$annotationName];
	}

	/**
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile
	 * @param int $pointer
	 * @return \SlevomatCodingStandard\Helpers\Annotation\Annotation[][]
	 */
	public static function getAnnotations(File $phpcsFile, int $pointer): array
	{
		$annotations = [];

		$docCommentOpenToken = DocCommentHelper::findDocCommentOpenToken($phpcsFile, $pointer);
		if ($docCommentOpenToken === null) {
			return $annotations;
		}

		$tokens = $phpcsFile->getTokens();
		$i = $docCommentOpenToken + 1;
		while ($i < $tokens[$docCommentOpenToken]['comment_closer']) {
			if ($tokens[$i]['code'] !== T_DOC_COMMENT_TAG) {
				$i++;
				continue;
			}

			$annotationStartPointer = $i;
			$annotationEndPointer = $i;

			// Fix for wrong PHPCS parsing
			$parenthesesLevel = substr_count($tokens[$i]['content'], '(') - substr_count($tokens[$i]['content'], ')');
			$annotationCode = $tokens[$i]['content'];

			for ($j = $i + 1; $j <= $tokens[$docCommentOpenToken]['comment_closer']; $j++) {
				if ($tokens[$j]['code'] === T_DOC_COMMENT_CLOSE_TAG) {
					$i = $j;
					break;
				}

				if ($tokens[$j]['code'] === T_DOC_COMMENT_TAG && $parenthesesLevel === 0) {
					$i = $j;
					break;
				}

				if ($tokens[$j]['code'] === T_DOC_COMMENT_STAR) {
					continue;
				}

				if (in_array($tokens[$j]['code'], [T_DOC_COMMENT_TAG, T_DOC_COMMENT_STRING], true)) {
					$annotationEndPointer = $j;
				} elseif ($tokens[$j]['code'] === T_DOC_COMMENT_WHITESPACE) {
					if (array_key_exists($j - 1, $tokens) && $tokens[$j - 1]['code'] === T_DOC_COMMENT_STAR) {
						continue;
					}
					if (array_key_exists($j + 1, $tokens) && $tokens[$j + 1]['code'] === T_DOC_COMMENT_STAR) {
						continue;
					}
				}

				$parenthesesLevel += substr_count($tokens[$j]['content'], '(') - substr_count($tokens[$j]['content'], ')');
				$annotationCode .= $tokens[$j]['content'];
			}

			$annotationName = $tokens[$annotationStartPointer]['content'];
			$annotationParameters = null;
			$annotationContent = null;
			if (preg_match('~^(@[-a-zA-Z\\\\]+)(?:\((.*)\))?(?:\\s+(.+))?($)~s', trim($annotationCode), $matches) !== 0) {
				$annotationName = $matches[1];
				$annotationParameters = trim($matches[2]);
				if ($annotationParameters === '') {
					$annotationParameters = null;
				}
				$annotationContent = trim($matches[3]);
				if ($annotationContent === '') {
					$annotationContent = null;
				}
			}

			$parsedContent = null;
			if ($annotationContent !== null) {
				$parsedContent = self::parseAnnotationContent($annotationName, $annotationContent);
				if ($parsedContent instanceof InvalidTagValueNode) {
					$parsedContent = null;
				}
			}

			$className = self::getAnnotationClassName($annotationName);
			$annotations[$annotationName][] = new $className($annotationName, $annotationStartPointer, $annotationEndPointer, $annotationParameters, $annotationContent, $parsedContent);
		}

		return $annotations;
	}

	private static function getAnnotationClassName(string $annotationName): string
	{
		$mapping = [
			'@param' => ParameterAnnotation::class,
			'@return' => ReturnAnnotation::class,
			'@var' => VariableAnnotation::class,
			'@throws' => ThrowsAnnotation::class,
			'@property' => PropertyAnnotation::class,
			'@property-read' => PropertyAnnotation::class,
			'@property-write' => PropertyAnnotation::class,
			'@method' => MethodAnnotation::class,
		];

		return array_key_exists($annotationName, $mapping) ? $mapping[$annotationName] : GenericAnnotation::class;
	}

	private static function parseAnnotationContent(string $annotationName, string $annotationContent): PhpDocTagValueNode
	{
		$tokens = new TokenIterator(self::getPhpDocLexer()->tokenize($annotationContent));
		$phpDocParser = self::getPhpDocParser();
		$methodReflection = new ReflectionMethod($phpDocParser, 'parseTagValue');
		$methodReflection->setAccessible(true);
		return $methodReflection->invoke($phpDocParser, $tokens, $annotationName);
	}

	private static function getPhpDocLexer(): Lexer
	{
		static $phpDocLexer;

		if ($phpDocLexer === null) {
			$phpDocLexer = new Lexer();
		}

		return $phpDocLexer;
	}

	private static function getPhpDocParser(): PhpDocParser
	{
		static $phpDocParser;

		if ($phpDocParser === null) {
			$phpDocParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
		}

		return $phpDocParser;
	}

}
