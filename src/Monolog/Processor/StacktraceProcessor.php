<?php
namespace Iamapen\MonologKit\Monolog\Processor;
use Monolog\Processor\ProcessorInterface;

/**
 * Stacktraceを付加するProcessor
 *
 * カオスコードのリファクタリング時に使えるかもしれない
 */
class StacktraceProcessor implements ProcessorInterface
{
	/** @var int */
	protected $options = DEBUG_BACKTRACE_IGNORE_ARGS;
	/** @var int */
	protected $limit = 0;

	/**
	 * @return array The processed record
	 */
	public function __invoke(array $record)
	{
		$stack = debug_backtrace($this->options, $this->limit);

		// __invoke(), call_user_func(), Monolog\\Logger::addRecord() の記録を除外
		$skipClassesPartials = ['Monolog\\', __CLASS__];
		foreach ($stack as $stackItem) {
			foreach ($skipClassesPartials as $part) {
				if (isset($stackItem['class']) && strpos($stackItem['class'], $part) !== false) {
					array_shift($stack);
					continue 2;
				}
			}
		}

		$record['extra']['trace'] = $stack;
		return $record;
	}

	/**
	 * @param int $options
	 * @return static
	 */
	public function setOptions($options)
	{
		$this->options = $options;
		return $this;
	}

	/**
	 * @param int $limit
	 * @return static
	 */
	public function setLimit($limit)
	{
		$this->limit = $limit;
		return $this;
	}
}
