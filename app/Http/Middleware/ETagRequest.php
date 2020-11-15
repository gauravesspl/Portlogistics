<?php
namespace App\Http\Middleware;
use Closure;
use Cache;
use Log;
use Illuminate\Http\Response;

class ETagRequest
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
         if ($request->ajax()) {
                $request->merge([
                 'isAjaxRequest' => true
                ]);        
        }  
      if ($request->isMethod('GET')) {
            if(!empty($request->cache_array))
            {
                $org_id = $request->cache_array['organization_id'];
                $user_id = $request->cache_array['user_id'];
                $cache_key = $request->cache_array['cache_key'];
                $key = $org_id.':'.$user_id.':'.$cache_key;
                $requestDataCache = Cache::get($key);
                $requestJson = json_encode($requestDataCache);
                $etag = md5($requestJson);
                $requestEtag = str_replace('"', '', $request->getETags());
                Log::info(json_encode($requestEtag));
                if($requestEtag && $requestEtag[0]  == $etag) {
                    if(!empty($requestDataCache))
                    {
                        $requestDataCache = $requestDataCache[$org_id][$user_id][$cache_key];
                        $cacheSource = $requestDataCache['cache_array']['source'];
                        $cacheView = $requestDataCache['view'];
                        $cacheData =  $requestDataCache['response'];
                        $cacheData = (array) $cacheData;
                        Log::info('Cache Hit');
                        if($cacheSource == 'browser')
                        {
                            header("Etag: $etag"); 
                            header("HTTP/1.1 304 Not Modified"); 
                            return new response(view($cacheView,$cacheData));
                        }
                        if($cacheSource == 'api')
                        {
                            header("Etag: $etag"); 
                            header("HTTP/1.1 304 Not Modified"); 
                            return response()->json($cacheData, 200);                            
                        }
                    }
                } 
                Log::info('Cache Miss');
            }
        }
        Log::info('Cache Miss Request');
        return $next($request);
    }
}