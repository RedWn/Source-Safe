<?php

namespace App\Http\Middleware;

use App\Models\Folder;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // if (app()->environment('local')) {
        //     $url = substr($request->getUri(), strpos($request->getUri(), "api/") + 4);
        //     $log = [
        //         'URI' => str_replace('\/', '/', $url),
        //         'method' => $request->getMethod(),
        //         'user' => $request->user()->username
        //     ];
        //     if ($request->has("fileIDs")) {
        //         $log["fileIDs"] = $request->fileIDs;
        //     }
        //     if ($request->has("file_ids")) {
        //         $log["file_ids"] = $request->file_ids;
        //     }
        //     if ($request->has("parent_folder_id")) {
        //         $log["parent_folder_id"] = $request->parent_folder_id;
        //         $parentFolder = Folder::findOrFail($request->input("parent_folder_id"));
        //         $log['project_id'] = $parentFolder->project_id;
        //     }
        //     if ($request->has("filename")) {
        //         $log["filename"] = $request->filename;
        //     }
        //     if ($request->has("name")) {
        //         $log["name"] = $request->name;
        //     }

        // Log::info(json_encode($log));
        // }

        return $response;
    }
}
