<?php

namespace App\Http\Controllers\API\User;

use App\Services\UserService;
use App\Http\Resources\UserResource;
use App\Http\Controllers\API\APIController;
use App\Http\Requests\API\ListUserAPIRequest;
use App\Http\Resources\UserResourceCollection;
use App\Http\Requests\API\RegisterUserAPIRequest;
use App\Http\Requests\API\UpdateUserAPIRequest;
use Illuminate\Http\Request;

class UserAPIController extends APIController
{
    /**
     * Undocumented variable
     *
     * @var UserService
     */
    private $userService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->setServices();
    }

    private function setServices()
    {
        $this->userService = new UserService();
    }
    /**
     * List all auth users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListUserAPIRequest $request)
    {
        $users = $this->userService->query(
            $request->get('skip'),
            $request->get('limit')
        );
        if (!$users->count()) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/users.plural')])
            );
        }
        return $this->sendResponse(
            new UserResourceCollection($users),
            __('messages.retrieved', ['model' => __('models/users.plural')])
        );
    }
    /**
     * Register a new user
     *
     * @param RegisterUserAPIRequest $request
     * @return Model
     */
    public function store(RegisterUserAPIRequest $request)
    {
        $input = $request->except('avatar');
        $avatar = $request->file('avatar');
        $user = $this->userService->save($input, $avatar);

        return $this->sendResponse(
            new UserResource($user),
            __('messages.saved', ['model' => __('models/users.singular')])
        );
    }

    /**
     * show data user
     *
     * @param RegisterUserAPIRequest $request
     * @return Model
     */
    public function show($id, Request $request)
    {
        $input = $request->all();
        $user = $this->userService->findOrFail($id);

        return $this->sendResponse(
            new UserResource($user),
            __('messages.retrieved', ['model' => __('models/users.singular')])
        );
    }

    /**
     * Update a new user
     *
     * @param RegisterUserAPIRequest $request
     * @return Model
     */
    public function update($id, UpdateUserAPIRequest $request)
    {

        $input = $request->except('avatar');
        $avatar = $request->file('avatar');


        $user = $this->userService->update($id, $input, $avatar);

        return $this->sendResponse(
            new UserResource($user),
            __('messages.saved', ['model' => __('models/users.singular')])
        );
    }


    /**
     * Deactive an User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeActiveStatus($id)
    {
        $active = $this->userService->isActive($id);
        if ($active) {
            return $this->deactive($id);
        } else {
            return $this->active($id);
        }
    }
    /**
     * Deactive an User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactive($id)
    {
        $user = $this->userService->deactive($id);
        return $this->sendSuccess(
            __('auth.deactive_success')
        );
    }

    /**
     * Deactive an User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function active($id)
    {
        $user = $this->userService->active($id);
        return $this->sendSuccess(
            __('auth.active_success')
        );
    }

    /**
     * Update a new user
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $qtdDel = $this->userService->delete($id);

        return $this->sendSuccess(
            __('messages.deleted', ['model' => __('models/users.singular')])
        );
    }

    /**
     * Get avatar url for ajax refresh.
     *
     * @return string
     */
    public function getAvatarUrl()
    {
        return auth()->user()->avatar_url;
    }
    /**
     * Delete avatar image.
     */
    public function avatarDelete(UserService $userService)
    {
        $user = auth()->user();
        $userService->avatarDelete($user);
        return $this->sendSuccess(
            __('messages.deleted_avatar', ['model' => __('models/users.singular')])
        );
    }

    public function avatarUpload(Request $request, UserService $userService)
    {
        $user = auth()->user();
        $avatar = $request->file('avatar');
        $userService->avatarUpload($user, $avatar);
        return $this->sendSuccess(
            __('messages.upload_avatar', ['model' => __('models/users.singular')])
        );
    }
}
