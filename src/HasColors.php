<?php
namespace Flynn314\Console;

use Flynn314\Console\Display\Display;
use Flynn314\Console\Formatter\ConsoleOutputFormatter;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait HasColors
{
    /**
     * @var null|OutputInterface
     */
    protected $output = null;

    /**
     * @var null|InputInterface
     */
    protected $input = null;

    /**
     * @var Display
     */
    protected $display = null;

    public function setInput(InputInterface $input): void
    {
        $this->input = $input;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
        $this->display = new Display($this->output);

        $outputFormatter = new ConsoleOutputFormatter();
        $outputFormatter->setOutputStyle($this->output);
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        if (function_exists('pcntl_signal')) {
    	    declare(ticks=1);
            pcntl_signal(SIGTERM, [&$this, 'onTerminate']);
            pcntl_signal(SIGINT, [&$this, 'onTerminate']);
            //pcntl_signal(SIGHUP, [&$this, 'onRestart']);
        }
        if (!$this->beforeExecute($input, $output)) {
            return 1;
        }
        $status = parent::execute($input, $output);

        $this->afterExecute($input, $output);

        return $status;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function onExecute(InputInterface $input, OutputInterface $output): ?int
    {
        return $this->execute($input, $output);
    }

    public function onTerminate()
    {
    	$this->beforeTerminate();
    	$this->afterTerminate();

        exit;
    }

    protected function beforeExecute(InputInterface $input, OutputInterface $output): bool
    {
        $this->setInput($input);
        $this->setOutput($output);

        return true;
    }

    protected function afterExecute(InputInterface $input, OutputInterface $output): void
    {
    }

    protected function beforeTerminate(): void
    {
	    echo PHP_EOL;
    }

    protected function afterTerminate(): void
    {
    	$this->displayWarning('Command is terminated', 'TERMINATED');
    }


    /**
     * Display on the same line
     *
     * @param string $message
     * @param string $tag
     */
    public function displaySl(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, false);
    }

    /**
     * Display on new line
     * @param string $message
     * @param string $tag
     */
    public function display(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true);
    }

    public function displayInfoSl(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, false, 'info');
    }
    public function displayInfo(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true, 'info');
    }

    public function displaySuccessSl(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, false, 'success');
    }
    public function displaySuccess(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true, 'success');
    }

    public function displayWarningSl(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, false, 'warning');
    }
    public function displayWarning(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true, 'warning');
    }

    public function displayErrorSl(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, false, 'error');
    }
    public function displayError(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true, 'error');
    }

    public function displayImportantSl(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, false, 'important');
    }
    public function displayImportant(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true, 'important');
    }

    public function displayExecTime()
    {
        $this->displayImportant(sprintf('Execution time (s): %f', $this->display->getExecTime()), 'EOF');
    }
}
