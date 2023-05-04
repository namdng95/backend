<?php

namespace App\Http\Controllers;

use App\Http\Requests\Message\GetMessageDetailRequest;
use App\Http\Requests\Message\GetListMessagesRequest;
use App\Http\Requests\Message\CreateMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Services\Message\Actions\GetMessageDetailAction;
use App\Services\Message\Actions\GetListMessagesAction;
use App\Services\Message\Actions\CreateMessageAction;
use App\Services\Message\Actions\DeleteMessageAction;
use App\Services\Message\Actions\UpdateMessageAction;
use Illuminate\Contracts\Container\BindingResolutionException;
use Throwable;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param GetListMessagesRequest $request GetListMessagesRequest
     *
     * @return mixed
     */
    public function index(GetListMessagesRequest $request): mixed
    {
        $data = $request->validated();
        $result = (new GetListMessagesAction())->handle($data);

        return response()->success($result);
    }

    /**
     *  Display the specified resource.
     *
     * @param int                     $id      ID
     * @param GetMessageDetailRequest $request GetMessageDetailRequest
     *
     * @return mixed
     */
    public function show(int $id, GetMessageDetailRequest $request): mixed
    {
        $data = $request->validated();
        $result = (new GetMessageDetailAction())->handle($id, $data);

        return response()->success($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateMessageRequest $request CreateMessageRequest
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function store(CreateMessageRequest $request): mixed
    {
        $data = $request->validated();
        $user = (new CreateMessageAction())->handle($data);

        return response()->success($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int                  $id      ID
     * @param UpdateMessageRequest $request UpdateMessageRequest
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function update(int $id, UpdateMessageRequest $request, ): mixed
    {
        $data = $request->validated();
        $result = (new UpdateMessageAction())->handle($id, $data);

        return response()->success($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id ID
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws Throwable
     */
    public function destroy(int $id): mixed
    {
        (new DeleteMessageAction())->handle($id);

        return response()->successWithoutData();
    }
}
