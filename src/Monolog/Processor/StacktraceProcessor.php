<?php
declare(strict_types=1);

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
        $skipClassesPartials = ['Monolog\\', static::class];
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
     * @return $this
     */
    public function setOptions(int $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }
}
