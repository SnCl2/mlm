<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;
use App\Models\User;
use App\Models\Shop;
use App\Models\Management;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for admin
     */
    public function index()
    {
        $notifications = Notification::with(['creator', 'user', 'shop'])
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new notification
     */
    public function create()
    {
        $users = User::select('id', 'name', 'email', 'referral_code')->get();
        $shops = Shop::select('id', 'name', 'owner_name', 'email')->get();
        
        return view('admin.notifications.create', compact('users', 'shops'));
    }

    /**
     * Store a newly created notification
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error,announcement',
            'recipient_type' => 'required|in:user,shop,all',
            'recipient_id' => 'nullable|integer',
            'send_email' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $admin = Auth::guard('management')->user();
            
            if ($request->recipient_type === 'all') {
                // Send to all users and shops
                $this->sendToAll($request, $admin);
            } else {
                // Send to specific user or shop
                $this->sendToSpecific($request, $admin);
            }

            DB::commit();

            return redirect()->route('admin.notifications.index')
                ->with('success', 'Notification sent successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to send notification: ' . $e->getMessage()]);
        }
    }

    /**
     * Send notification to all users and shops
     */
    private function sendToAll(Request $request, $admin)
    {
        // Send to all users
        $users = User::all();
        foreach ($users as $user) {
            $notification = Notification::create([
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'recipient_type' => 'user',
                'recipient_id' => $user->id,
                'created_by' => $admin->id,
                'metadata' => $request->metadata ?? null,
            ]);

            // Send email if requested
            if ($request->send_email && $user->email) {
                $this->sendEmailNotification($user, $notification);
            }
        }

        // Send to all shops
        $shops = Shop::all();
        foreach ($shops as $shop) {
            $notification = Notification::create([
                'title' => $request->title,
                'message' => $request->message,
                'type' => $request->type,
                'recipient_type' => 'shop',
                'recipient_id' => $shop->id,
                'created_by' => $admin->id,
                'metadata' => $request->metadata ?? null,
            ]);

            // Send email if requested
            if ($request->send_email && $shop->email) {
                $this->sendEmailNotification($shop, $notification);
            }
        }
    }

    /**
     * Send notification to specific user or shop
     */
    private function sendToSpecific(Request $request, $admin)
    {
        $notification = Notification::create([
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'recipient_type' => $request->recipient_type,
            'recipient_id' => $request->recipient_id,
            'created_by' => $admin->id,
            'metadata' => $request->metadata ?? null,
        ]);

        // Send email if requested
        if ($request->send_email) {
            $recipient = $request->recipient_type === 'user' 
                ? User::find($request->recipient_id)
                : Shop::find($request->recipient_id);
                
            if ($recipient && $recipient->email) {
                $this->sendEmailNotification($recipient, $notification);
            }
        }
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification($recipient, $notification)
    {
        $subject = "Digital Care MLM - " . $notification->title;
        
        Mail::raw(
            "Dear " . $recipient->name . ",\n\n" .
            $notification->message . "\n\n" .
            "Best regards,\nDigital Care MLM Team",
            function ($message) use ($recipient, $subject) {
                $message->to($recipient->email)
                        ->subject($subject);
            }
        );
    }

    /**
     * Display the specified notification
     */
    public function show(Notification $notification)
    {
        $notification->load(['creator', 'user', 'shop']);
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notification $notification)
    {
        // Check if the current user is authorized to mark this notification as read
        $guard = request()->get('guard');
        
        if (!$guard) {
            // Auto-detect the guard
            if (Auth::guard('management')->check()) {
                $guard = 'management';
            } elseif (Auth::guard('shop')->check()) {
                $guard = 'shop';
            } elseif (Auth::guard('web')->check()) {
                $guard = 'web';
            } else {
                return response()->json(['success' => false, 'message' => 'User not authenticated']);
            }
        }
        
        $user = Auth::guard($guard)->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated']);
        }

        // Check if this notification belongs to the current user
        $recipientType = $guard === 'shop' ? 'shop' : 'user';
        
        if ($notification->recipient_type !== $recipientType || $notification->recipient_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized to mark this notification as read']);
        }
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read for current user
     */
    public function markAllAsRead(Request $request)
    {
        // Try to determine the guard automatically if not provided
        $guard = $request->get('guard');
        
        if (!$guard) {
            // Auto-detect the guard based on which user is authenticated
            if (Auth::guard('management')->check()) {
                $guard = 'management';
            } elseif (Auth::guard('shop')->check()) {
                $guard = 'shop';
            } elseif (Auth::guard('web')->check()) {
                $guard = 'web';
            } else {
                return response()->json(['success' => false, 'message' => 'User not authenticated']);
            }
        }
        
        $user = Auth::guard($guard)->user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated']);
        }

        // Management users don't have notifications to mark as read
        if ($guard === 'management') {
            return response()->json(['success' => true, 'message' => 'No notifications to mark as read']);
        }

        $recipientType = $guard === 'shop' ? 'shop' : 'user';
        
        Notification::where('recipient_type', $recipientType)
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Get notifications for current user (API endpoint)
     */
    public function getNotifications(Request $request)
    {
        // Try to determine the guard automatically if not provided
        $guard = $request->get('guard');
        
        if (!$guard) {
            // Auto-detect the guard based on which user is authenticated
            if (Auth::guard('management')->check()) {
                $guard = 'management';
            } elseif (Auth::guard('shop')->check()) {
                $guard = 'shop';
            } elseif (Auth::guard('web')->check()) {
                $guard = 'web';
            } else {
                return response()->json(['notifications' => [], 'unread_count' => 0]);
            }
        }
        
        $user = Auth::guard($guard)->user();
        
        if (!$user) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        // Management users don't receive notifications, they send them
        if ($guard === 'management') {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $recipientType = $guard === 'shop' ? 'shop' : 'user';
        
        $notifications = Notification::where('recipient_type', $recipientType)
            ->where('recipient_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->where('is_read', false)->count()
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy(Notification $notification)
    {
        $notification->delete();
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notification deleted successfully!');
    }

    /**
     * Get user/shop list for AJAX
     */
    public function getRecipients(Request $request)
    {
        $type = $request->get('type');
        $search = $request->get('search', '');

        if ($type === 'user') {
            $recipients = User::where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('referral_code', 'like', "%{$search}%")
                ->select('id', 'name', 'email', 'referral_code')
                ->limit(20)
                ->get();
        } else {
            $recipients = Shop::where('name', 'like', "%{$search}%")
                ->orWhere('owner_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->select('id', 'name', 'owner_name', 'email')
                ->limit(20)
                ->get();
        }

        return response()->json($recipients);
    }
}