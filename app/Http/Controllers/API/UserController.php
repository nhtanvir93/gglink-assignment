<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserRequest;
use App\Repositories\UserRepository;
use App\Repositories\GroupUserRepository;
use App\Repositories\AvatarRepository;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    private $userRepository;
    private $groupUserRepository;
    private $avatarRepository;

    public function __construct(UserRepository $userRepository,
                                GroupUserRepository $groupUserRepository,
                                AvatarRepository $avatarRepository)
    {
        $this->userRepository = $userRepository;
        $this->groupUserRepository = $groupUserRepository;
        $this->avatarRepository = $avatarRepository;
    }

    public function store(UserRequest $request)
    {
        $tableFields1 = ['username', 'email', 'password', 'groups', 'avatar'];
        $tableFields2 = ['username', 'email', 'password', 'groups'];

        $validatedData = $request->validated();

        $inputs = count($tableFields1) == count($validatedData) ?
            array_combine($tableFields1, $validatedData) : array_combine($tableFields2, $validatedData);

        $avatar = isset($inputs['avatar']) ? $inputs['avatar'] : null;
        unset($inputs['avatar']);

        $groups = isset($inputs['groups']) ? $inputs['groups'] : [config('custom_settings.default_group_id')];
        unset($inputs['groups']);

        $avatarDirectory = config('custom_settings.upload_paths.avatar');

        DB::transaction(function() use ($inputs, $avatar, $groups, $avatarDirectory) {
            $inputs['avatar_id'] = isset($avatar) ?
                $this->avatarRepository->uploadAndCreate($avatar, $avatarDirectory) : null;

            $userId = $this->userRepository->create($inputs);

            $this->groupUserRepository->createAll($groups, $userId);
        });

        return response()->json([
            'Success' => true,
            'Message' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }

    public function show()
    {
        try {
            $id = request()->query('Id', 0);

            $user = $this->userRepository->getDetails($id);

            $user = $user->toArray();

            if(!$user['avatar']) {
                $defaultAvatarPath = config('custom_settings.default_avatar_path');

                $user['avatar']['path'] = $defaultAvatarPath;
                $user['avatar']['url'] = url($defaultAvatarPath);
            }

            return response()->json([
                'Success' => true,
                'Message' => Response::$statusTexts[Response::HTTP_OK],
                'Data' => $user
            ], Response::HTTP_OK);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'Success' => false,
                'Message' => 'UserNotFound'
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    public function update(UserRequest $request)
    {
        $tableFields1 = ['email', 'password', 'groups', 'avatar'];
        $tableFields2 = ['email', 'password', 'groups'];

        $validatedData = $request->validated();

        $id = $validatedData['Id'];
        unset($validatedData['Id']);

        $inputs = count($tableFields1) == count($validatedData) ?
            array_combine($tableFields1, $validatedData) : array_combine($tableFields2, $validatedData);

        $user = $this->userRepository->getDetails($id);

        $avatar = isset($inputs['avatar']) ? $inputs['avatar'] : null;
        unset($inputs['avatar']);

        $groups = isset($inputs['groups']) ? $inputs['groups'] : [config('custom_settings.default_group_id')];
        unset($inputs['groups']);

        $avatarDirectory = config('custom_settings.upload_paths.avatar');

        DB::transaction(function() use ($inputs, $user, $avatar, $groups, $avatarDirectory) {
            $inputs['avatar_id'] = $user->avatar_id && $avatar ?
                $this->avatarRepository->uploadAndUpdate($user->avatar_id, $avatar, $avatarDirectory) :
                (
                    !$user->avatar_id && $avatar ? $this->avatarRepository->uploadAndCreate($avatar, $avatarDirectory) : null
                );

            $this->userRepository->update($inputs, $user->id);

            $this->groupUserRepository->updateAll($groups, $user->id);
        });

        return response()->json([
            'Success' => true,
            'Message' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }

    public function destroy(UserDeleteRequest $request)
    {
        $id = $request->validated()['Id'];

        $this->userRepository->delete($id);

        return response()->json([
            'Success' => true,
            'Message' => Response::$statusTexts[Response::HTTP_OK]
        ], Response::HTTP_OK);
    }

    public function index()
    {
        try {
            $users = $this->userRepository->getAllUsers();

            return response()->json([
                'Success' => true,
                'Message' => Response::$statusTexts[Response::HTTP_OK],
                'Data' => $users
            ], Response::HTTP_OK);
        } catch(QueryException $exception) {

            return response()->json([
                'Success' => false,
                'Message' => 'NoDataUser'
            ], Response::HTTP_NOT_ACCEPTABLE);
        }
    }
}

