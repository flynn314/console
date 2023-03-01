<?php
namespace Flynn314\Console\Display;

use Symfony\Component\Console\Output\OutputInterface;

class Display
{
    private OutputInterface $output;

    private ?float $execStartTime = null;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function displayBase(string $message, string $tag='', bool $nl=true, string $style='default'): void
    {
        if (null !== $this->execStartTime) {
            $this->execStartTime = microtime(true);
        }

        $text = '';
        if ($tag) {
            $text .= '<'.$style.'tag> '.mb_strtoupper($tag).' </'.$style.'tag> ';
        }
        $text .= '<'.$style.'text>'.$message.'</'.$style.'text>';

        if ($nl) {
            $this->output->writeln($text);
        } else {
            $this->output->write($text);
        }
    }

    public function getExecTime(): float
    {
        return microtime(true) - $this->execStartTime;
    }
}
