<?php
// Selalu nyalakan session sebelum output apa pun
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Ambil Halaman Utama dari Query String (?page=...)
$page = $_GET['page'] ?? 'home';

// 2. Handle Form POST (Login, Register, Logout, Register Event)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Process Login
    if ($_POST['action'] === 'login') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!empty($username)) {
            $_SESSION['user'] = [
                'name' => $username,
                'nim' => '254312801', // Default NIM jika tidak diisi
                'email' => strtolower(str_replace(' ', '', $username)) . '@gmail.com',
                'phone' => '081234567890'
            ];
            header('Location: belajar.php?page=home');
            exit;
        }
    }

    // Process Register Account
    if ($_POST['action'] === 'register_account') {
        $fullname = trim($_POST['fullname'] ?? '');
        $nim = trim($_POST['nim'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if (!empty($fullname)) {
            $_SESSION['user'] = [
                'name' => $fullname,
                'nim' => $nim,
                'email' => $email,
                'phone' => '081234567890'
            ];
            header('Location: belajar.php?page=home');
            exit;
        }
    }

    // Process Event Registration
    if ($_POST['action'] === 'register_event') {
        $_SESSION['last_ticket'] = [
            'event_name' => $_POST['event_name'] ?? 'Seminar Future Leaders Forum',
            'date' => $_POST['event_date'] ?? 'Kamis, 22 Januari 2027',
            'location' => $_POST['event_location'] ?? 'Auditorium Lt.4',
            'fullname' => $_POST['fullname'] ?? ($_SESSION['user']['name'] ?? 'Peserta'),
            'nim' => $_POST['nim'] ?? ($_SESSION['user']['nim'] ?? '-'),
            'phone' => $_POST['phone'] ?? ($_SESSION['user']['phone'] ?? '-'),
            'ticket_id' => 'SFLF-' . rand(1000, 9999) . '-' . rand(1000, 9999)
        ];
        
        header('Location: belajar.php?page=ticket');
        exit;
    }
}

// 3. Handle Process Logout
if ($page === 'logout') {
    session_unset();
    session_destroy();
    header('Location: belajar.php?page=login');
    exit;
}

// 4. Cek Proteksi Login
$is_logged_in = isset($_SESSION['user']) && is_array($_SESSION['user']);

// Jika belum login dan mencoba mengakses halaman selain login/register, paksa ke halaman login
if (!$is_logged_in && !in_array($page, ['login', 'register_account'])) {
    $page = 'login';
}

// If already logged in but visiting login page, redirect to home
if ($is_logged_in && in_array($page, ['login', 'register_account'])) {
    $page = 'home';
}

// 5. Whitelist Halaman Valid
$allowed_pages = ['home', 'explore', 'detail', 'register', 'ticket', 'my_events', 'profile', 'logout_confirm', 'login', 'register_account'];
if (!in_array($page, $allowed_pages)) {
    $page = $is_logged_in ? 'home' : 'login';
}

// Variabel data user aktif
$user_name = $_SESSION['user']['name'] ?? '';
$user_nim = $_SESSION['user']['nim'] ?? '';
$user_phone = $_SESSION['user']['phone'] ?? '';
$user_email = $_SESSION['user']['email'] ?? '';
$initial_letter = !empty($user_name) ? strtoupper(substr($user_name, 0, 1)) : 'U';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CampusHub - Find, Join, Connect</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-950 text-white min-h-screen flex flex-col font-sans">

    <?php if ($is_logged_in): ?>
    <!-- TOP HEADER / NAVBAR (Hanya tampil jika sudah Login) -->
    <nav class="bg-slate-900/90 backdrop-blur-md border-b border-slate-800 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="belajar.php?page=home" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-tr from-purple-600 to-indigo-500 rounded-xl flex items-center justify-center font-bold text-xl shadow-lg shadow-indigo-500/30">
                        C
                    </div>
                    <div>
                        <span class="text-xl font-extrabold bg-gradient-to-r from-white via-slate-200 to-indigo-300 bg-clip-text text-transparent">CampusHub</span>
                        <p class="text-[9px] text-indigo-400 tracking-widest uppercase -mt-1 hidden sm:block">Find. Join. Connect.</p>
                    </div>
                </a>

                <!-- Nav Menu Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="belajar.php?page=home" class="<?php echo $page === 'home' ? 'text-indigo-400 font-bold border-b-2 border-indigo-400 pb-1' : 'text-slate-300 hover:text-white'; ?> transition">Home</a>
                    <a href="belajar.php?page=explore" class="<?php echo $page === 'explore' ? 'text-indigo-400 font-bold border-b-2 border-indigo-400 pb-1' : 'text-slate-300 hover:text-white'; ?> transition">Explore</a>
                    <a href="belajar.php?page=my_events" class="<?php echo $page === 'my_events' ? 'text-indigo-400 font-bold border-b-2 border-indigo-400 pb-1' : 'text-slate-300 hover:text-white'; ?> transition">My Events</a>
                    <a href="belajar.php?page=profile" class="<?php echo $page === 'profile' ? 'text-indigo-400 font-bold border-b-2 border-indigo-400 pb-1' : 'text-slate-300 hover:text-white'; ?> transition">Profile</a>
                </div>

                <!-- Profile Quick View -->
                <a href="belajar.php?page=profile" class="flex items-center gap-3 bg-slate-800/80 hover:bg-slate-800 px-3 py-1.5 rounded-full border border-slate-700 transition">
                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-sm">
                        <?php echo $initial_letter; ?>
                    </div>
                    <span class="text-sm font-medium hidden sm:inline"><?php echo htmlspecialchars($user_name); ?></span>
                </a>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <!-- CONTENT ROUTER -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full flex items-center justify-center">

        <?php switch ($page): 
            case 'login': ?>
            <!-- LOGIN PAGE -->
            <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
                <div class="text-center mb-8">
                    <div class="w-14 h-14 bg-gradient-to-tr from-purple-600 to-indigo-500 rounded-2xl flex items-center justify-center font-bold text-2xl mx-auto mb-3 shadow-lg shadow-indigo-500/30">
                        C
                    </div>
                    <h2 class="text-2xl font-bold">Welcome To CampusHub</h2>
                    <p class="text-slate-400 text-sm">Discovers what’s happening on campus</p>
                </div>

                <form action="belajar.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="login">
                    <div>
                        <label class="block text-sm font-medium mb-1">Username / Name</label>
                        <input type="text" name="username" placeholder="Enter Your Name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Password</label>
                        <input type="password" name="password" placeholder="••••••••" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition mt-2">
                        Login
                    </button>
                </form>

                <p class="text-center text-sm text-slate-400 mt-6">
                    Don't have an account? <a href="belajar.php?page=register_account" class="text-indigo-400 hover:underline font-semibold">Register</a>
                </p>
            </div>
            <?php break; ?>

        <?php case 'register_account': ?>
            <!-- CREATE ACCOUNT PAGE -->
            <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold">Create Account</h2>
                    <p class="text-slate-400 text-sm">Let's get started with CampusHub</p>
                </div>

                <form action="belajar.php" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="register_account">
                    <div>
                        <label class="block text-sm font-medium mb-1">Full Name</label>
                        <input type="text" name="fullname" placeholder="Enter Your Name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" placeholder="Enter Your Email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Student ID (NIM)</label>
                        <input type="text" name="nim" placeholder="Enter Your Student ID" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition mt-2">
                        Create Account
                    </button>
                </form>

                <p class="text-center text-sm text-slate-400 mt-6">
                    Already have an account? <a href="belajar.php?page=login" class="text-indigo-400 hover:underline font-semibold">Login</a>
                </p>
            </div>
            <?php break; ?>

        <?php case 'home': ?>
            <!-- HOME PAGE -->
            <div class="w-full">
                <div class="bg-gradient-to-r from-indigo-900/50 to-purple-900/50 p-8 rounded-3xl border border-indigo-500/20 mb-10 shadow-2xl">
                    <h1 class="text-3xl sm:text-5xl font-extrabold mb-3">Discovers What’s <br><span class="text-indigo-400">Happening on Campus</span></h1>
                    <p class="text-slate-300 mb-6 max-w-xl">Temukan seminar, workshop, dan kompetisi menarik kampus secara praktis!</p>
                    
                    <form action="belajar.php" method="GET" class="flex gap-3 max-w-2xl">
                        <input type="hidden" name="page" value="explore">
                        <div class="relative flex-1">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" name="q" placeholder="Search events..." class="w-full bg-slate-900/90 border border-slate-700 rounded-xl py-3.5 pl-11 pr-4 text-white focus:outline-none focus:border-indigo-500">
                        </div>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 font-semibold px-6 py-3.5 rounded-xl transition">Search</button>
                    </form>
                </div>

                <!-- Categories -->
                <section class="mb-10">
                    <h2 class="text-xl font-bold mb-4">Categories</h2>
                    <div class="flex gap-3 overflow-x-auto pb-2">
                        <a href="belajar.php?page=explore" class="bg-indigo-600 text-white px-5 py-2 rounded-xl text-sm font-medium">All</a>
                        <a href="belajar.php?page=explore&cat=seminar" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-5 py-2 rounded-xl text-sm font-medium">Seminar</a>
                        <a href="belajar.php?page=explore&cat=workshop" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-5 py-2 rounded-xl text-sm font-medium">Workshop</a>
                        <a href="belajar.php?page=explore&cat=lomba" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-5 py-2 rounded-xl text-sm font-medium">Lomba</a>
                    </div>
                </section>

                <!-- Featured Events -->
                <section>
                    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-fire text-amber-500"></i> Featured Events
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-lg hover:border-indigo-500/50 transition">
                            <div class="h-44 bg-gradient-to-r from-purple-800 to-indigo-900 flex items-center justify-center p-4 relative">
                                <span class="absolute top-3 right-3 bg-indigo-600 text-xs px-3 py-1 rounded-full font-semibold">WORKSHOP</span>
                                <h3 class="text-2xl font-black text-center">UI/UX DESIGN WORKSHOP</h3>
                            </div>
                            <div class="p-5">
                                <h4 class="text-lg font-bold mb-2">UI/UX Design Workshop</h4>
                                <p class="text-slate-400 text-sm mb-1"><i class="fa-regular fa-calendar text-indigo-400 w-5"></i> Senin, 24 Desember 2026</p>
                                <p class="text-slate-400 text-sm mb-1"><i class="fa-regular fa-clock text-indigo-400 w-5"></i> 08.00 WIB</p>
                                <p class="text-slate-400 text-sm mb-4"><i class="fa-solid fa-location-dot text-indigo-400 w-5"></i> Auditorium Lt.3 PNJ</p>
                                <a href="belajar.php?page=detail" class="block text-center bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2.5 rounded-xl transition">Lihat Detail</a>
                            </div>
                        </div>

                        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-lg hover:border-indigo-500/50 transition">
                            <div class="h-44 bg-gradient-to-r from-pink-800 to-purple-900 flex items-center justify-center p-4 relative">
                                <span class="absolute top-3 right-3 bg-pink-600 text-xs px-3 py-1 rounded-full font-semibold">FESTIVAL</span>
                                <h3 class="text-2xl font-black text-center">CUMLAUDE FEST</h3>
                            </div>
                            <div class="p-5">
                                <h4 class="text-lg font-bold mb-2">Cumlaude Fest</h4>
                                <p class="text-slate-400 text-sm mb-1"><i class="fa-regular fa-calendar text-indigo-400 w-5"></i> Minggu, 10 Januari 2027</p>
                                <p class="text-slate-400 text-sm mb-1"><i class="fa-regular fa-clock text-indigo-400 w-5"></i> 18.00 WIB</p>
                                <p class="text-slate-400 text-sm mb-4"><i class="fa-solid fa-location-dot text-indigo-400 w-5"></i> Balairung Kampus</p>
                                <a href="belajar.php?page=detail" class="block text-center bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2.5 rounded-xl transition">Lihat Detail</a>
                            </div>
                        </div>

                        <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-lg hover:border-indigo-500/50 transition">
                            <div class="h-44 bg-gradient-to-r from-blue-800 to-indigo-900 flex items-center justify-center p-4 relative">
                                <span class="absolute top-3 right-3 bg-blue-600 text-xs px-3 py-1 rounded-full font-semibold">LOMBA</span>
                                <h3 class="text-2xl font-black text-center">ANKA FOOTBALL</h3>
                            </div>
                            <div class="p-5">
                                <h4 class="text-lg font-bold mb-2">AnKa Football Competition</h4>
                                <p class="text-slate-400 text-sm mb-1"><i class="fa-regular fa-calendar text-indigo-400 w-5"></i> Jumat, 15 Januari 2027</p>
                                <p class="text-slate-400 text-sm mb-1"><i class="fa-regular fa-clock text-indigo-400 w-5"></i> 17.00 WIB</p>
                                <p class="text-slate-400 text-sm mb-4"><i class="fa-solid fa-location-dot text-indigo-400 w-5"></i> Lapangan SPIRIT</p>
                                <a href="belajar.php?page=detail" class="block text-center bg-indigo-600 hover:bg-indigo-500 text-white font-medium py-2.5 rounded-xl transition">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php break; ?>

        <?php case 'explore': ?>
            <!-- EXPLORE EVENTS PAGE -->
            <div class="w-full">
                <div class="mb-8">
                    <h1 class="text-3xl font-bold mb-1">Explore Events</h1>
                    <p class="text-slate-400">Find something interesting on your campus...</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex gap-4 items-center">
                        <div class="w-28 h-28 bg-indigo-900/60 rounded-xl flex items-center justify-center font-bold text-center text-xs p-2">
                            SEMINAR FUTURE LEADERS
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-lg mb-1">Seminar Future Leaders Forum</h3>
                            <p class="text-slate-400 text-xs mb-1"><i class="fa-regular fa-calendar text-indigo-400"></i> Kamis, 22 Januari 2027 (13.14 WIB)</p>
                            <p class="text-slate-400 text-xs mb-3"><i class="fa-solid fa-location-dot text-indigo-400"></i> Auditorium Lt.4</p>
                            <a href="belajar.php?page=detail" class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white text-xs px-4 py-2 rounded-lg font-medium">View Detail</a>
                        </div>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex gap-4 items-center">
                        <div class="w-28 h-28 bg-purple-900/60 rounded-xl flex items-center justify-center font-bold text-center text-xs p-2">
                            CIFEST 2027
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-lg mb-1">CIFEST 2027</h3>
                            <p class="text-slate-400 text-xs mb-1"><i class="fa-regular fa-calendar text-indigo-400"></i> Sabtu, 21 Februari 2027 (18.30 WIB)</p>
                            <p class="text-slate-400 text-xs mb-3"><i class="fa-solid fa-location-dot text-indigo-400"></i> Balairung</p>
                            <a href="belajar.php?page=detail" class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white text-xs px-4 py-2 rounded-lg font-medium">View Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php break; ?>

        <?php case 'detail': ?>
            <!-- DETAIL EVENT PAGE -->
            <div class="max-w-4xl w-full mx-auto">
                <a href="belajar.php?page=home" class="inline-flex items-center gap-2 text-indigo-400 hover:text-indigo-300 mb-6 font-medium">
                    <i class="fa-solid fa-arrow-left"></i> Kembali
                </a>

                <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl">
                    <div class="h-64 bg-gradient-to-r from-indigo-900 via-purple-900 to-slate-900 flex items-center justify-center p-8">
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-center tracking-wide">Seminar Future Leaders Forum</h1>
                    </div>

                    <div class="p-8">
                        <div class="flex flex-wrap gap-6 mb-8 p-4 bg-slate-950/60 rounded-2xl border border-slate-800">
                            <div class="flex items-center gap-3">
                                <i class="fa-regular fa-calendar text-xl text-indigo-400"></i>
                                <div>
                                    <p class="text-xs text-slate-400">Tanggal</p>
                                    <p class="font-semibold text-sm">Kamis, 22 Januari 2027</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fa-regular fa-clock text-xl text-indigo-400"></i>
                                <div>
                                    <p class="text-xs text-slate-400">Waktu</p>
                                    <p class="font-semibold text-sm">13.14 WIB</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-location-dot text-xl text-indigo-400"></i>
                                <div>
                                    <p class="text-xs text-slate-400">Lokasi</p>
                                    <p class="font-semibold text-sm">Auditorium Lt.4</p>
                                </div>
                            </div>
                        </div>

                        <h3 class="text-xl font-bold mb-3">About Event</h3>
                        <p class="text-slate-300 leading-relaxed mb-8">
                            Seminar Future Leaders Forum is a strategic platform designed to connect academic insights with real-world industry demands. Bringing together top industry practitioners and scholars, this seminar explores current trends, collaborative opportunities, and essential leadership skills needed in today’s dynamic market.
                        </p>

                        <a href="belajar.php?page=register" class="block w-full text-center bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-600/30 transition text-lg">
                            Register Now
                        </a>
                    </div>
                </div>
            </div>
            <?php break; ?>

        <?php case 'register': ?>
            <!-- FORM REGISTRASI EVENT -->
            <div class="max-w-md w-full mx-auto">
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl">
                    <h2 class="text-2xl font-extrabold mb-1">Registration</h2>
                    <p class="text-slate-400 text-sm mb-6">Lengkapi data pendaftaran Anda.</p>

                    <form action="belajar.php" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="register_event">
                        <input type="hidden" name="event_name" value="Seminar Future Leaders Forum">
                        <input type="hidden" name="event_date" value="Kamis, 22 Januari 2027">
                        <input type="hidden" name="event_location" value="Auditorium Lt.4">

                        <div>
                            <label class="block text-sm font-medium mb-1">Full Name</label>
                            <input type="text" name="fullname" value="<?php echo htmlspecialchars($user_name); ?>" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Student ID (NIM)</label>
                            <input type="text" name="nim" value="<?php echo htmlspecialchars($user_nim); ?>" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Phone Number</label>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($user_phone); ?>" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-indigo-500">
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition mt-4">
                            Confirm Registration
                        </button>
                    </form>
                </div>
            </div>
            <?php break; ?>

        <?php case 'ticket': ?>
            <!-- TIKET DIGITAL (QR CODE) -->
            <?php $ticket = $_SESSION['last_ticket'] ?? [
                'event_name' => 'Seminar Future Leaders Forum',
                'date' => 'Kamis, 22 Januari 2027',
                'location' => 'Auditorium Lt.4',
                'fullname' => $user_name ?: 'Peserta',
                'nim' => $user_nim ?: '254312801',
                'ticket_id' => 'SFLF-8842-1092'
            ]; ?>
            <div class="max-w-md w-full mx-auto">
                <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl p-6 text-center">
                    <div class="inline-block bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-4 py-1 rounded-full text-xs font-bold mb-4">
                        <i class="fa-solid fa-circle-check mr-1"></i> Registration Confirmed
                    </div>
                    <h2 class="text-xl font-bold mb-1"><?php echo htmlspecialchars($ticket['event_name']); ?></h2>
                    <p class="text-xs text-slate-400 mb-6"><?php echo htmlspecialchars($ticket['date']); ?> • <?php echo htmlspecialchars($ticket['location']); ?></p>

                    <!-- Mock QR Code -->
                    <div class="bg-white p-6 rounded-2xl w-48 h-48 mx-auto flex items-center justify-center shadow-inner mb-6">
                        <i class="fa-solid fa-qrcode text-slate-900 text-8xl"></i>
                    </div>

                    <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 text-left space-y-2 text-sm mb-6">
                        <div class="flex justify-between"><span class="text-slate-400">Ticket ID:</span> <span class="font-mono font-bold text-indigo-400"><?php echo htmlspecialchars($ticket['ticket_id']); ?></span></div>
                        <div class="flex justify-between"><span class="text-slate-400">Nama:</span> <span><?php echo htmlspecialchars($ticket['fullname']); ?></span></div>
                        <div class="flex justify-between"><span class="text-slate-400">NIM:</span> <span><?php echo htmlspecialchars($ticket['nim']); ?></span></div>
                    </div>

                    <a href="belajar.php?page=my_events" class="block w-full bg-slate-800 hover:bg-slate-700 text-white font-medium py-3 rounded-xl transition">
                        Lihat Event Saya
                    </a>
                </div>
            </div>
            <?php break; ?>

        <?php case 'my_events': ?>
            <!-- MY EVENTS PAGE -->
            <div class="w-full">
                <h1 class="text-2xl font-bold mb-6">My Registered Events</h1>
                <?php if (isset($_SESSION['last_ticket'])): ?>
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-lg mb-1"><?php echo htmlspecialchars($_SESSION['last_ticket']['event_name']); ?></h3>
                            <p class="text-slate-400 text-xs"><i class="fa-regular fa-calendar text-indigo-400"></i> <?php echo htmlspecialchars($_SESSION['last_ticket']['date']); ?></p>
                        </div>
                        <a href="belajar.php?page=ticket" class="bg-indigo-600 hover:bg-indigo-500 text-white text-xs px-4 py-2 rounded-lg font-medium">View Ticket</a>
                    </div>
                <?php else: ?>
                    <p class="text-slate-400">Belum ada event yang didaftarkan.</p>
                <?php endif; ?>
            </div>
            <?php break; ?>

        <?php case 'profile': ?>
            <!-- PROFILE PAGE -->
            <div class="max-w-md w-full mx-auto bg-slate-900 border border-slate-800 rounded-3xl p-8 shadow-2xl text-center">
                <div class="w-20 h-20 bg-indigo-600 rounded-full flex items-center justify-center font-bold text-3xl mx-auto mb-4">
                    <?php echo $initial_letter; ?>
                </div>
                <h2 class="text-2xl font-bold mb-1"><?php echo htmlspecialchars($user_name); ?></h2>
                <p class="text-slate-400 text-sm mb-6"><?php echo htmlspecialchars($user_email); ?></p>

                <div class="bg-slate-950 p-4 rounded-2xl border border-slate-800 text-left space-y-3 text-sm mb-6">
                    <div class="flex justify-between"><span class="text-slate-400">NIM:</span> <span><?php echo htmlspecialchars($user_nim); ?></span></div>
                    <div class="flex justify-between"><span class="text-slate-400">Telepon:</span> <span><?php echo htmlspecialchars($user_phone); ?></span></div>
                </div>

                <a href="belajar.php?page=logout" class="block w-full bg-rose-600/20 hover:bg-rose-600/30 text-rose-400 border border-rose-500/30 font-bold py-3 rounded-xl transition">
                    Logout
                </a>
            </div>
            <?php break; ?>

        <?php endswitch; ?>

    </main>

</body>
</html>