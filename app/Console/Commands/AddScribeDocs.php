<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use ReflectionClass;
use ReflectionMethod;

class AddScribeDocs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scribe:add-docs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically add documentation comments to routes and controllers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $routes = Route::getRoutes();
        $updatedFiles = [];
    
        foreach ($routes as $route) {
            $action = $route->getAction();
    
            // Check if the route has a controller
            if (isset($action['controller']) && str_contains($action['controller'], '@')) {
                [$controller, $method] = explode('@', $action['controller']);
                $this->addDocCommentToController($controller, $method, $route, $updatedFiles);
            } else {
                $this->warn("Skipping route: " . $route->uri() . " (No valid controller found)");
            }
        }
    
        if (count($updatedFiles)) {
            $this->info("Documentation added to the following files:");
            foreach ($updatedFiles as $file) {
                $this->info($file);
            }
        } else {
            $this->info("No updates made. All routes are already documented.");
        }
    }
    

    /**
     * Add doc comments to a controller's method if missing.
     */
    private function addDocCommentToController($controller, $method, $route, &$updatedFiles)
    {
        try {
            $reflection = new ReflectionMethod($controller, $method);
            $controllerReflection = new ReflectionClass($controller);

            // Skip if the method already has a doc comment
            if ($reflection->getDocComment()) {
                return;
            }

            $filePath = $controllerReflection->getFileName();
            $fileContents = file($filePath);

            $startLine = $reflection->getStartLine() - 1;
            $routePath = $route->uri();
            $httpMethods = implode(', ', $route->methods());

            $defaultComment = <<<EOD
    /**
     * @group Auto-Generated
     *
     * Description for {$method} endpoint.
     *
     * @route {$httpMethods} {$routePath}
     * @response {
     *   "example": "data"
     * }
     */
EOD;

            // Inject the comment above the method
            array_splice($fileContents, $startLine, 0, $defaultComment . PHP_EOL);

            file_put_contents($filePath, implode('', $fileContents));

            if (!in_array($filePath, $updatedFiles)) {
                $updatedFiles[] = $filePath;
            }
        } catch (\Exception $e) {
            $this->error("Failed to add doc for {$controller}@{$method}: {$e->getMessage()}");
        }
    }
}



