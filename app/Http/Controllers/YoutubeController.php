<?php

namespace App\Http\Controllers;

use Alaouy\Youtube\Facades\Youtube;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class YoutubeController extends Controller
{

    public $videoid;
    public $finalTranscript = ['text' => ''];

    public function __construct(Request $request)
    {   
        $this->videoid = $request->videoid;
    }

    public function getTranscript()
    {
        // Define the Python script and its location
        $script = base_path('transcript.py');
        
        // Create the process to run the script
        $process = new Process(['python3', '-u', $script, $this->videoid]);
        
        // Run the process
        $process->run();
        // Check if the process was successful
        if (!$process->isSuccessful()) {
        // Log the error output
        \Log::error('Process failed: ' . $process->getErrorOutput());
        throw new ProcessFailedException($process);
    }

        // Get the output and decode it
        $output = $process->getOutput();

        // Log the output for debugging
        \Log::info('Process output: ' . $output);
        \Log::info('Process output: ' . $process->getOutput());
        \Log::info('Process error output: ' . $process->getErrorOutput());
        $transcript = json_decode($output, true);
        
        if ($transcript === null && json_last_error() !== JSON_ERROR_NONE) {
            // Log JSON decoding errors
            \Log::error('JSON decoding error: ' . json_last_error_msg());
        }
        foreach ($transcript as $key => $value) {
            $this->finalTranscript['text'] .= ' ' . $value['text'];
        }

        $this->finalTranscript['metal'] = $this->getVideoName();
        
        return response()->json($this->finalTranscript);
        
    }
    private function getVideoName()
    {
        $apiKey = env('GOOGLE_API_KEY');
        $script = base_path('videoname.py');
        
        // Create the process to run the script
        $process = new Process(['python3', $script, $this->videoid, $apiKey]);
        
        // Run the process
        $process->run();

        // Check if the process was successful
        if (!$process->isSuccessful()) {
            // Log the error output
            \Log::error('Process failed: ' . $process->getErrorOutput());
            throw new ProcessFailedException($process);
        }

        // Get the output and decode it
        $output = $process->getOutput();

        // Log the output for debugging
        \Log::info('Process output: ' . $output);

        $result = json_decode($output, true);
        
        if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
            // Log JSON decoding errors
            \Log::error('JSON decoding error: ' . json_last_error_msg());
        }

        return $result;
    }
}