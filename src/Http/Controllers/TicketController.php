<?php

namespace Liberty\Tickets\Http\Controllers;

use Illuminate\Routing\Controller;
use Liberty\Tickets\Http\Requests\AssignTicketRequest;
use Liberty\Tickets\Http\Requests\ChangePriorityRequest;
use Liberty\Tickets\Http\Requests\StoreTicketRequest;
use Liberty\Tickets\Http\Requests\StoreTicketCommentRequest;
use Liberty\Tickets\Http\Requests\UpdateTicketRequest;
use Liberty\Tickets\Models\Ticket;
use Liberty\Tickets\Services\TicketService;

class TicketController extends Controller
{
    public function __construct(private TicketService $service) {}

    public function index()
    {
        $user = request()->user();
        $level = $user->ticketLevel();

        $tickets = Ticket::query()
            ->where('created_level', '<=', $level)
            ->latest()
            ->paginate(20);

        return view('tickets::tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets::tickets.create');
    }

    public function store(StoreTicketRequest $request)
    {
        $ticket = $this->service->create($request->validated(), $request->user());

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('status', 'Ticket submitted.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['comments']);

        $userModel = config('auth.providers.users.model');
        $users = $userModel ? $userModel::whereIn('id', $ticket->comments->pluck('user_id')->filter())
            ->get()->keyBy('id') : collect();

        return view('tickets::tickets.show', compact('ticket', 'users'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $this->service->update($ticket, $request->validated(), $request->user());

        return back()->with('status', 'Ticket updated.');
    }

    public function changePriority(ChangePriorityRequest $request, Ticket $ticket)
    {
        $this->service->changePriority($ticket, (int) $request->priority, $request->user());

        return back()->with('status', 'Priority updated.');
    }

    public function escalate(Ticket $ticket)
    {
        $this->service->requestEscalation($ticket, request()->user());

        return back()->with('status', 'Escalation requested.');
    }

    public function verifyBug(Ticket $ticket)
    {
        $this->service->verifyBug($ticket, request()->user());

        return back()->with('status', 'Bug verified.');
    }

    public function assign(AssignTicketRequest $request, Ticket $ticket)
    {
        $this->service->assign($ticket, (int) $request->assigned_to, $request->user());

        return back()->with('status', 'Ticket assigned.');
    }

    public function resolve(Ticket $ticket)
    {
        $this->service->resolve($ticket, request()->user());

        return back()->with('status', 'Ticket resolved.');
    }

    public function storeComment(StoreTicketCommentRequest $request, Ticket $ticket)
    {
        $this->service->addComment($ticket, $request->input('body'), $request->user());

        return back()->with('status', 'Comment added.');
    }
}
