<?php declare(strict_types=1);

namespace Starburst\Utils;

/**
 * @readonly
 */
final class ThrowableProperties implements \JsonSerializable, \Stringable
{
	public static function fromThrowable(\Throwable $e): self
	{
		return new self(
			get_class($e),
			$e->getMessage(),
			$e->__toString(),
			$e->getCode(),
			$e->getFile(),
			$e->getLine(),
			self::getOther($e),
			self::getTrace($e),
			$e->getPrevious() === null
				? null
				: self::fromThrowable($e->getPrevious()),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function getOther(\Throwable $e) : array
	{
		$skip = [
			'message',
			'string',
			'code',
			'file',
			'line',
			'trace',
			'previous',
		];

		$other = [];
		foreach ((new \ReflectionClass($e))->getProperties() as $rp) {
			$prop = $rp->getName();
			if (in_array($prop, $skip)) {
				continue;
			}

			$other[$prop] = $rp->getValue($e);
		}

		return $other;
	}

	/**
	 * @return list<array{
	 *     file:string,
	 *     line:int,
	 *     function:string,
	 *     class:string,
	 *     type:string,
	 * }>
	 */
	private static function getTrace(\Throwable $e) : array
	{
		$trace = [];

		/**
		 * @var array{
		 *     function: string,
		 *     line: int,
		 *     file: string,
		 *     class: string,
		 *     object: object,
		 *     type: string,
		 *     args: mixed[],
		 * } $info
		 */
		foreach ($e->getTrace() as $info) {
			unset($info['args']);
			$trace[] = $info;
		}

		return $trace;
	}

	public function __construct(
		public string $class,
		public string $message,
		public string $string,
		public int $code,
		public string $file,
		public int $line,
		/** @var array<string, mixed> */
		public array $other,
		/** @var list<array{file:string,line:int,function:string,class:string,type:string}> */
		public array $trace,
		public ?ThrowableProperties $previous,
	) {}


	public function __toString() : string
	{
		return $this->string;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function jsonSerialize() : array
	{
		return $this->getArrayCopy();
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getArrayCopy() : array
	{
		return get_object_vars($this);
	}
}
