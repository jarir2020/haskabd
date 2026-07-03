<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Toastr;

// Contact messages inbox controller -- added 2026-04-15
class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::query();

        // Filter by read/unread
        if ($request->filled('status')) {
            $query->where('is_read', $request->status === 'read' ? 1 : 0);
        }

        // Search name, email, phone, subject
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name',    'like', "%$s%")
                  ->orWhere('email',   'like', "%$s%")
                  ->orWhere('phone',   'like', "%$s%")
                  ->orWhere('subject', 'like', "%$s%");
            });
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        match($sort) {
            'oldest' => $query->oldest(),
            'name'   => $query->orderBy('name'),
            default  => $query->latest(),
        };

        $messages = $query->paginate(20)->withQueryString();
        $unreadCount = ContactMessage::where('is_read', 0)->count();

        return view('backEnd.contact_messages.index', compact('messages', 'unreadCount'));
    }

    public function show($id)
    {
        $msg = ContactMessage::findOrFail($id);

        // Mark as read on open
        if (!$msg->is_read) {
            $msg->update(['is_read' => true]);
        }

        return view('backEnd.contact_messages.show', compact('msg'));
    }

    public function destroy($id)
    {
        ContactMessage::findOrFail($id)->delete();
        Toastr::success('Message deleted.', 'Success');
        return redirect()->route('admin.contact_messages.index');
    }
}
