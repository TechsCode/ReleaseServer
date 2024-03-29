<?php

namespace App\Http\Controllers;

use App\Models\TechsCodePlugin;
use App\Models\UpdateRequest;
use App\Models\UpdateRequestStatus;
use App\Services\TechsCodeAuth;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AuthenticateController extends Controller
{

    /**
     * @throws Exception
     */
    public function authenticatePage(Request $request, string $update_token){
        if (empty($update_token)){
            throw new Exception('Invalid update token');
        }
        session()->put('update_token', $update_token);
        return redirect(TechsCodeAuth::getAuthUrl(route('authenticate.callback')));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function authenticateCallback(){
        $update_token = session()->get('update_token');
        if (empty($update_token)){
            return view('pages.error')
                ->with([
                    'error_message' => 'Unknown update token',
                    'show_join_button' => true
                ]);
        }
        session()->forget('update_token');

        try{
            $auth_token = config('services.techscode.auth.token');
            $ot_token = request()->get('token');
            if (empty($ot_token)){
                return view('pages.error')
                    ->with([
                        'error_message' => 'Authentication failed',
                        'show_join_button' => true
                    ]);
            }

            $user_info = TechsCodeAuth::getUser($auth_token, $ot_token);

            if (empty($user_info)){
                return view('pages.error')
                    ->with([
                        'error_message' => 'Authentication failed',
                        'show_join_button' => true
                    ]);
            }

            /** @var UpdateRequest $update_request */
            $update_request = UpdateRequest::query()
                ->where('update_token', $update_token)
                ->first();
            if (empty($update_request)){
                return view('pages.error')
                    ->with([
                        'error_message' => 'Update request not found',
                        'show_join_button' => true
                    ]);
            }
            $user_roles = $user_info['roles'];
            $support_server_roles = $user_roles['support_server'];

            $has_admin_access = $this->hasAdminAccess($support_server_roles);
            $update_request->has_beta_access = $this->hasBetaAccess($support_server_roles);

            $allowed_plugins = $this->getAllowedPlugins($support_server_roles);
            if (empty($allowed_plugins) && !$has_admin_access){
                $update_request->status = UpdateRequestStatus::UNAUTHORIZED;
                $update_request->save();
                return view('pages.error')
                    ->with([
                        'error_message' => 'You do not have access to any plugins.<br>Please verify your roles on the support server.',
                        'show_join_button' => true
                    ]);
            }
            if(!in_array($update_request->plugin_name, $allowed_plugins) && !$has_admin_access){
                $update_request->status = UpdateRequestStatus::UNAUTHORIZED;
                $update_request->save();
                return view('pages.error')
                    ->with([
                        'error_message' => "You do not have access to $update_request->plugin_name.<br>Please verify your roles on the support server.",
                        'show_join_button' => true
                    ]);
            }

            if ($has_admin_access){
                $update_request->has_beta_access = true;
                $allowed_plugins = TechsCodePlugin::getValidPluginKeys();
            }

            $update_request->allowed_plugins = implode(',', $allowed_plugins);
            $update_request->status = UpdateRequestStatus::AUTHORIZED;

            $update_request->save();

            return view('pages.success');
        }catch (Exception $e){
            \Log::debug($e->getMessage());
            return view('pages.error')
                ->with([
                    'error_message' => 'Unknown Error',
                    'show_join_button' => true
                ]);
        } catch (GuzzleException|NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            \Log::debug($e->getMessage());
            return view('pages.error')
                ->with([
                    'error_message' => 'Unknown Error',
                    'show_join_button' => true
                ]);
        }
    }

    private function getAllowedPlugins(array $user_roles): array
    {
        $allowed_plugins = [];

        $verified_role_id = config('services.techscode.role_ids.verified');
        if(!in_array($verified_role_id, $user_roles))
        {
            return $allowed_plugins;
        }

        $plugins = TechsCodePlugin::$valid_plugins;
        foreach ($plugins as $key => $plugin){
            $role_id = config('services.techscode.role_ids.' . $key);
            if(in_array($role_id, $user_roles)){
                $allowed_plugins[] = $key;
            }
        }

        return $allowed_plugins;
    }

    public function hasBetaAccess(array $user_roles): bool
    {
        $patreon_role_id = config('services.techscode.role_ids.patreon');
        $patreon_adventurer_role_id = config('services.techscode.role_ids.patreon_adventurer');
        $patreon_pioneer_role_id = config('services.techscode.role_ids.patreon_pioneer');
        $patreon_coding_wizard_role_id = config('services.techscode.role_ids.patreon_coding_wizard');

        if(
            in_array($patreon_role_id, $user_roles) &&
            (
                in_array($patreon_adventurer_role_id, $user_roles) ||
                in_array($patreon_pioneer_role_id, $user_roles) ||
                in_array($patreon_coding_wizard_role_id, $user_roles)
            )
        ){
            return true;
        }else{
            return false;
        }
    }

    public function hasAdminAccess(array $user_roles): bool
    {
        $support_role_id = config('services.techscode.role_ids.support');
        $development_role_id = config('services.techscode.role_ids.development');
        $leadership_role_id = config('services.techscode.role_ids.leadership');
        $marketing_role_id = config('services.techscode.role_ids.marketing');

        if(
            in_array($support_role_id, $user_roles) ||
            in_array($development_role_id, $user_roles) ||
            in_array($leadership_role_id, $user_roles) ||
            in_array($marketing_role_id, $user_roles)
        ){
            return true;
        }else{
            return false;
        }
    }

}
