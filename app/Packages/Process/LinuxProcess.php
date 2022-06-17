<?php

namespace App\Packages\Process;

use Symfony\Component\Process\Process;

class LinuxProcess
{
    protected array $processes = [];

    public function __construct(public Process $process_builder)
    {

    }

    /**
     * @throws \Exception
     */
    public function getCurrentProcesses($current_user_only = true): array
    {

        $this->process_builder->run();

        if (!$this->process_builder->isSuccessful()) {
            throw new \Exception("The 'ps' process was not successful: " . $this->process_builder->getErrorOutput());
        }
        // Get the process output
        $output = $this->process_builder->getOutput();
        // Convert the output into an arrow of rows
        $output = explode("\n", $output);

        // Get the leading row which happens to be the header row
        $header = array_shift($output);
        // Split the header row on spaces
        $header = preg_split('/ +/', $header);
        // Get the number of columns from the header
        $header_count = count($header);

        // Initialize an array to hold all the processes
        $this->processes = array();

        foreach ($output as $process) {
            if (empty($process)) {
                continue;
            }
            $process = preg_split('/ +/', $process);

            // The first few columns will match the header, but the last column (COMMAND) could be in multiple
            // array fields because processes are broken up by spaces. So grab the known headers first and then
            // add any remaining array fields from process to the COMMAND field
            $tmp_process = array();
            foreach ($header as $header_name) {
                // Cut the column out of the process
                $column = array_splice($process, 0, 1);
                $tmp_process[$header_name] = $column[0];
            }
            if (!empty($process)) {
                // Add the remaining process fields to the COMMAND field in the process definition
                $tmp_process[$header[$header_count - 1]] .= " " . implode(" ", $process);
            }
            $this->processes[$tmp_process["PID"]] = $tmp_process;
        }

        return $this->processes;
    }

}
