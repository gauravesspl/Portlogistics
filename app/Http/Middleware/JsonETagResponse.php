<?php
namespace App\Http\Middleware;
use Closure;
use Cache;
use Log;
class JsonETagResponse
{
    /**
     * Implement Etag support
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get response
        $response = $next($request);
        header('Cache-Control: public');
        // If this was a GET request...
        if ($request->isMethod('get'))
        {
            // Generate Etag
            $getData = $response->getOriginalContent();
            if(!empty($getData['key']))
            {
                $key = $getData['key'];
                $requestDataCache = Cache::get($key);
                $requestJson = json_encode($requestDataCache);
                $etag = bcrypt($requestJson);
                $requestEtag = str_replace('"', '', $request->getETags());
                // Check to see if Etag has changed
                if ($requestEtag && $requestEtag[0] == $etag)
                {
                    $response->setNotModified();
                }
                Log::info($etag);
                Log::info($requestEtag);
                // Set Etag
                $response->setEtag($etag);
            }
            else
            {
                $response->headers->remove('Etag');
            }
        }
        else
        {
            $response->headers->remove('Etag');
        }
        // Send response
        return $response;
    }
}
