import { useEffect, useMemo, useRef, useState } from "react";
import { captureJpeg, startCamera, stopCamera } from "./cam.js";
import { distanceMeters, fmtCoord } from "./geo.js";
import { loadState, makeInitialState, resetState, saveState } from "./storage.js";

const nav = [
  { key: "dashboard", label: "Dashboard", group: "Menu Utama", icon: "M3 12l2-2 7-7 7 7M5 10v10h14V10" },
  { key: "attendance", label: "Presensi Wajah", group: "Menu Utama", icon: "M3 9a2 2 0 012-2h2l1-2h8l1 2h2a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" },
  { key: "leave", label: "Pengajuan Cuti", group: "Self Service", icon: "M8 7V3m8 4V3M5 11h14M5 21h14V7H5v14z" },
  { key: "payroll", label: "Slip Gaji", group: "Self Service", icon: "M9 8h6m-6 4h6m-6 4h6M7 3h10v18H7V3z" },
  { key: "directory", label: "Direktori", group: "Self Service", icon: "M17 20h5v-1a4 4 0 00-4-4M9 20H4v-1a4 4 0 014-4m7-8a3 3 0 11-6 0 3 3 0 016 0z" },
  { key: "admin", label: "Admin Dashboard", group: "Manajemen HR", icon: "M9 17v-6H5v6h4zm6 0V7h-4v10h4zm6 0V3h-4v14h4z", admin: true },
  { key: "employees", label: "Data Karyawan", group: "Manajemen HR", icon: "M17 20h5v-1a4 4 0 00-4-4M9 20H4v-1a4 4 0 014-4m7-8a3 3 0 11-6 0 3 3 0 016 0z", admin: true },
  { key: "recap", label: "Rekap Kehadiran", group: "Manajemen HR", icon: "M9 5h6m-8 4h10M7 13h10M7 17h6", admin: true },
  { key: "approvals", label: "Approval Center", group: "Manajemen HR", icon: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z", admin: true },
];

const announcements = [
  { type: "INFO", title: "Full demo client siap dicoba", content: "Kamera dan geolocation berjalan langsung dari browser client melalui HTTPS Netlify, tanpa runtime PHP." },
  { type: "HR", title: "Alur HRIS tersedia", content: "Client bisa melihat cuti, payroll, direktori, approval, dan rekap dalam mode simulasi." },
];

const leaveRequests = [
  { id: "LV-2026-001", employee: "Test Employee", type: "Annual Leave", range: "24 Apr 2026 - 25 Apr 2026", days: 2, status: "Pending", approver: "Administrator" },
  { id: "LV-2026-002", employee: "Dinda Prameswari", type: "Sick Leave", range: "21 Apr 2026", days: 1, status: "Approved", approver: "Administrator" },
  { id: "LV-2026-003", employee: "Raka Wijaya", type: "Personal Leave", range: "28 Apr 2026", days: 1, status: "Rejected", approver: "Administrator" },
];

const payrollRows = [
  { period: "April 2026", basic: 8500000, allowance: 1250000, deduction: 350000, status: "Ready" },
  { period: "March 2026", basic: 8500000, allowance: 1000000, deduction: 275000, status: "Paid" },
  { period: "February 2026", basic: 8200000, allowance: 900000, deduction: 210000, status: "Paid" },
];

const approvals = [
  { id: "APR-001", module: "Leave Request", requester: "Test Employee", detail: "Annual Leave 2 hari", status: "Waiting Review", age: "2 jam" },
  { id: "APR-002", module: "Attendance Correction", requester: "Dinda Prameswari", detail: "Koreksi check-in 08:06", status: "Need Decision", age: "1 hari" },
  { id: "APR-003", module: "Reimbursement", requester: "Raka Wijaya", detail: "Transport client visit", status: "Finance Check", age: "3 hari" },
];

function formatRupiah(value) {
  return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(value);
}

function useDemoState() {
  const [state, setState] = useState(() => loadState() || makeInitialState());
  useEffect(() => saveState(state), [state]);
  return [state, setState];
}

function Icon({ path }) {
  return (
    <svg className="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" d={path} />
    </svg>
  );
}

function Avatar({ name }) {
  return <div className="avatar">{name?.charAt(0)?.toUpperCase() || "U"}</div>;
}

function Login({ state, setState }) {
  const [email, setEmail] = useState("admin@gmail.com");
  const [password, setPassword] = useState("password");
  const [error, setError] = useState("");

  function login() {
    const found = state.users.find((item) => item.email.toLowerCase() === email.toLowerCase() && item.password === password);
    if (!found) {
      setError("Email/password demo salah. Pakai akun yang tersedia di panel kanan.");
      return;
    }
    setState((prev) => ({ ...prev, currentUser: found.id }));
  }

  return (
    <main className="login-page">
      <section className="login-hero">
        <div className="section-title">CoreHR Full Demo</div>
        <h1>Presensi wajah dengan GPS, siap dicoba client.</h1>
        <p>Full demo tanpa PHP ini mengikuti UI aplikasi utama. Kamera dan lokasi tetap real dari browser, sedangkan data demo berjalan di sisi client agar gratis tanpa backend.</p>
        <div className="login-points">
          <span>Camera permission real</span>
          <span>Geolocation real</span>
          <span>Data demo client-side</span>
        </div>
        <div className="demo-scope-note">
          <b>Catatan untuk client:</b> ini interactive preview terbatas. Kamera dan lokasi benar-benar berjalan, tetapi database, face matching produksi, approval, payroll, dan audit server disimulasikan agar aman dibuka dari portofolio.
        </div>
      </section>

      <section className="ui-card login-card">
        <div className="section-title">Login Demo</div>
        <h2>Masuk sebagai admin atau karyawan</h2>
        <label className="field">
          <span>Email</span>
          <input value={email} onChange={(event) => setEmail(event.target.value)} />
        </label>
        <label className="field">
          <span>Password</span>
          <input type="password" value={password} onChange={(event) => setPassword(event.target.value)} />
        </label>
        {error ? <div className="alert danger">{error}</div> : null}
        <button className="ui-button-primary full" type="button" onClick={login}>Masuk</button>
        <div className="alert">
          Demo terbatas: data hanya tersimpan di browser ini. Full version memakai backend, database, storage, audit log, dan security layer produksi.
        </div>
        <div className="demo-accounts">
          {state.users.map((account) => (
            <button
              key={account.id}
              type="button"
              onClick={() => {
                setEmail(account.email);
                setPassword(account.password);
                setError("");
              }}
            >
              <b>{account.role === "admin" ? "Admin" : "Karyawan"}</b>
              <span>{account.email} / password</span>
            </button>
          ))}
        </div>
      </section>
    </main>
  );
}

function Layout({ state, setState, user, page, setPage, children }) {
  const [open, setOpen] = useState(false);
  const [userMenu, setUserMenu] = useState(false);
  const [time, setTime] = useState(new Date());
  const links = nav.filter((item) => !item.admin || user.role === "admin");
  const groups = [...new Set(links.map((item) => item.group))];

  useEffect(() => {
    const timer = setInterval(() => setTime(new Date()), 1000);
    return () => clearInterval(timer);
  }, []);

  return (
    <div className="app-shell">
      <button className={`overlay ${open ? "show" : ""}`} type="button" onClick={() => setOpen(false)} aria-label="Tutup sidebar" />
      <aside className={`sidebar ${open ? "open" : ""}`}>
        <button className="brand" type="button" onClick={() => setPage(user.role === "admin" ? "admin" : "dashboard")}>
          <strong>CoreHR</strong>
          <span>Enterprise Portal</span>
        </button>
        <nav className="sidebar-scroll">
          {groups.map((group) => (
            <div className="nav-group" key={group}>
              <p>{group}</p>
              {links.filter((item) => item.group === group).map((item) => (
                <button key={item.key} type="button" className={page === item.key ? "active" : ""} onClick={() => { setPage(item.key); setOpen(false); }}>
                  <Icon path={item.icon} />
                  <span>{item.label}</span>
                </button>
              ))}
            </div>
          ))}
        </nav>
        <div className="sidebar-bottom">
          <button type="button" onClick={() => setPage("guide")}>Panduan</button>
          <button type="button" className="logout" onClick={() => setState((prev) => ({ ...prev, currentUser: null }))}>Keluar</button>
        </div>
      </aside>

      <section className="main-shell">
        <header className="topbar">
          <button className="hamburger" type="button" onClick={() => setOpen(true)}>☰</button>
          <label className="top-search">
            <span>⌕</span>
            <input placeholder="Search resources..." />
          </label>
          <div className="live-time">Live Time <b>{time.toLocaleTimeString("id-ID")}</b></div>
          <div className="profile-menu">
            <button type="button" onClick={() => setUserMenu((value) => !value)}><Avatar name={user.name} /><span>{user.name}</span></button>
            <div className={`profile-dropdown ${userMenu ? "show" : ""}`}>
              <p>Account</p>
              <b>{user.name}</b>
              <span>{user.email}</span>
              <button type="button" onClick={() => { setPage("profile"); setUserMenu(false); }}>Profil Saya</button>
              <button type="button" onClick={() => { setPage("guide"); setUserMenu(false); }}>Panduan</button>
              <button type="button" className="danger-text" onClick={() => setState((prev) => ({ ...prev, currentUser: null }))}>Keluar</button>
            </div>
          </div>
        </header>
        <main className="content">{children}</main>
      </section>
    </div>
  );
}

function StatCard({ label, value, tone = "" }) {
  return (
    <div className="ui-card stat-card">
      <div className="section-title">{label}</div>
      <div className={`stat-value ${tone}`}>{value}</div>
    </div>
  );
}

function Dashboard({ state, user, setPage }) {
  const today = new Date().toISOString().slice(0, 10);
  const myToday = state.attendances.filter((item) => item.userId === user.id && item.date === today);
  const hasIn = myToday.some((item) => item.type === "in");
  const hasOut = myToday.some((item) => item.type === "out");
  const status = !hasIn ? "Belum absen" : hasOut ? "Absensi selesai" : "Sudah check-in";

  return (
    <>
      <section className="ui-card hero-card">
        <div>
          <div className="section-title">Dashboard Karyawan</div>
          <h1>Ringkasan presensi harian Anda</h1>
          <p>Pantau status check-in, pengumuman aktif, dan riwayat absensi hari ini dalam satu tampilan yang rapi dan fokus.</p>
          <div className="actions">
            <button className="ui-button-primary" type="button" onClick={() => setPage("attendance")}>{hasIn && !hasOut ? "Lanjutkan Check-out" : "Buka Presensi"}</button>
            <button className="ui-button-secondary" type="button" onClick={() => setPage("profile")}>Lihat Profil</button>
          </div>
        </div>
        <div className="ui-card-muted status-card">
          <div className="section-title">Status Hari Ini</div>
          <h2>{status}</h2>
          <p><span>Check-in</span><b>{hasIn ? "Tercatat" : "Belum"}</b></p>
          <p><span>Check-out</span><b>{hasOut ? "Tercatat" : "Belum"}</b></p>
        </div>
      </section>

      <section className="stats-grid">
        <StatCard label="Status Hari Ini" value={status} tone={hasOut ? "success" : hasIn ? "primary" : "warning"} />
        <StatCard label="Pengumuman Aktif" value={announcements.length} tone="primary" />
        <StatCard label="Absensi Hari Ini" value={myToday.length} />
      </section>

      <section className="two-grid">
        <div className="ui-card panel">
          <div className="panel-head"><h2>Pengumuman Terbaru</h2><span className="badge">Internal</span></div>
          {announcements.map((item) => (
            <article className="mini-card" key={item.title}><span className="badge">{item.type}</span><h3>{item.title}</h3><p>{item.content}</p></article>
          ))}
        </div>
        <HistoryCard attendances={myToday} emptyText="Belum ada absensi hari ini." />
      </section>
    </>
  );
}

function AdminDashboard({ state }) {
  const today = new Date().toISOString().slice(0, 10);
  const employeeCount = state.users.filter((item) => item.role === "employee").length;
  const todays = state.attendances.filter((item) => item.date === today);
  const present = new Set(todays.filter((item) => item.type === "in").map((item) => item.userId)).size;

  return (
    <>
      <section className="admin-hero">
        <div>
          <div className="section-title">Admin Dashboard</div>
          <h1>Executive overview presensi & HRIS</h1>
          <p>Tampilan mobile-friendly untuk melihat KPI, aktivitas terbaru, dan modul administrasi tanpa terasa padat.</p>
        </div>
        <div className="admin-summary"><b>{Math.round((present / Math.max(employeeCount, 1)) * 100)}%</b><span>Attendance Rate</span></div>
      </section>
      <section className="stats-grid four">
        <StatCard label="Karyawan Aktif" value={employeeCount} />
        <StatCard label="Hadir Hari Ini" value={present} tone="success" />
        <StatCard label="Belum Hadir" value={Math.max(employeeCount - present, 0)} tone="warning" />
        <StatCard label="Transaksi" value={todays.length} tone="primary" />
      </section>
      <section className="two-grid">
        <HistoryCard attendances={state.attendances.slice(0, 12)} title="Recent Attendance Feed" emptyText="Belum ada aktivitas presensi." />
        <div className="ui-card panel">
          <div className="panel-head"><h2>Modul HRIS</h2><span className="badge">Demo</span></div>
          {["Data Karyawan", "Approval Center", "Payroll", "Performance Heatmap", "Audit Trail"].map((item) => (
            <div className="list-row" key={item}><div><b>{item}</b><span>View simulasi tersedia untuk presentasi client.</span></div><span className="badge">Ready</span></div>
          ))}
        </div>
      </section>
    </>
  );
}

function EmployeesView({ state }) {
  const employees = state.users.filter((item) => item.role === "employee");
  const active = employees.filter((item) => item.active).length;
  const branches = new Set(employees.map((item) => item.branch)).size;

  return (
    <>
      <section className="ui-card page-header">
        <div>
          <div className="section-title">Employee Master</div>
          <h1>Data induk karyawan HRIS</h1>
          <p>Halaman ini mengikuti struktur React utama: employee code, organisasi, status aktif, dan data administratif inti.</p>
        </div>
        <button className="ui-button-primary" type="button">Tambah Karyawan</button>
      </section>
      <section className="stats-grid">
        <StatCard label="Total Karyawan" value={employees.length} />
        <StatCard label="Karyawan Aktif" value={active} />
        <StatCard label="Cabang Aktif" value={branches} />
      </section>
      <DataTable
        title="Daftar Karyawan"
        subtitle="Data master siap dipakai untuk attendance, cuti, payroll, dan reporting."
        columns={["Karyawan", "Organisasi", "Status", "Kontak", "Aksi"]}
        rows={employees.map((employee) => [
          <div><b>{employee.name}</b><span>{employee.employeeCode} • {employee.email}</span></div>,
          <div><span>Jabatan: {employee.position}</span><span>Departemen: {employee.department}</span><span>Cabang: {employee.branch}</span></div>,
          <div className="table-badges"><span className={`badge ${employee.active ? "ok" : ""}`}>{employee.active ? "Aktif" : "Nonaktif"}</span><span className="badge">Wajah terdaftar</span></div>,
          <div><span>{employee.email}</span><span>+62 812 0000 {employee.employeeCode.slice(-3)}</span></div>,
          <div className="row-actions"><button>Detail</button><button>Edit</button></div>,
        ])}
      />
    </>
  );
}

function RecapView({ state }) {
  const today = new Date().toISOString().slice(0, 10);
  const employees = state.users.filter((item) => item.role === "employee");
  const todays = state.attendances.filter((item) => item.date === today);
  const present = new Set(todays.filter((item) => item.type === "in").map((item) => item.userId)).size;

  return (
    <>
      <section className="ui-card page-header">
        <div>
          <div className="section-title">Attendance Monitor</div>
          <h1>Rekap kehadiran dan validasi presensi</h1>
        </div>
        <button className="ui-button-secondary" type="button">Lihat Koreksi Absensi</button>
      </section>
      <section className="stats-grid five">
        <StatCard label="Total Staff" value={employees.length} />
        <StatCard label="Hadir" value={present} tone="success" />
        <StatCard label="Terlambat" value="0" />
        <StatCard label="Absen" value={Math.max(employees.length - present, 0)} tone="warning" />
        <StatCard label="Koreksi Pending" value="2" tone="primary" />
      </section>
      <DataTable
        title="Rekap Presensi"
        subtitle="Data demo dari browser client."
        columns={["Karyawan", "Organisasi", "Waktu", "Status", "Lokasi", "Aksi"]}
        rows={(state.attendances.length ? state.attendances : employees.slice(0, 2).map((employee, index) => ({
          id: `sample-${employee.id}`,
          userName: employee.name,
          type: index === 0 ? "in" : "out",
          createdAt: new Date().toISOString(),
          distanceMeters: 24 + index,
          userId: employee.id,
        }))).map((attendance) => {
          const employee = state.users.find((item) => item.id === attendance.userId);
          return [
            <div><b>{attendance.userName}</b><span>{attendance.type === "in" ? "Check-in" : "Check-out"}</span></div>,
            <div><span>{employee?.department || "-"}</span><span>{employee?.position || "-"}</span></div>,
            <div><span>Tanggal: {new Date(attendance.createdAt).toISOString().slice(0, 10)}</span><span>Jam: {new Date(attendance.createdAt).toLocaleTimeString("id-ID")}</span></div>,
            <div className="table-badges"><span className="badge ok">On time</span><span className="badge">Overtime 0m</span></div>,
            <div><span>{state.office.name}</span><span>{attendance.distanceMeters}m dari titik kantor</span></div>,
            <div className="row-actions"><button>Detail</button></div>,
          ];
        })}
      />
    </>
  );
}

function LeaveView({ user }) {
  const visibleRows = user.role === "admin" ? leaveRequests : leaveRequests.filter((item) => item.employee === user.name || item.employee === "Test Employee");
  const pending = visibleRows.filter((item) => item.status === "Pending").length;
  const approved = visibleRows.filter((item) => item.status === "Approved").length;

  return (
    <>
      <section className="ui-card page-header">
        <div>
          <div className="section-title">Leave Self Service</div>
          <h1>Pengajuan cuti dan saldo leave</h1>
          <p>Client bisa melihat bagaimana karyawan membuat request dan bagaimana status approval ditampilkan.</p>
        </div>
        <button className="ui-button-primary" type="button">Ajukan Cuti</button>
      </section>
      <section className="stats-grid">
        <StatCard label="Saldo Tahunan" value="10 hari" />
        <StatCard label="Menunggu Approval" value={pending} tone="warning" />
        <StatCard label="Disetujui" value={approved} tone="success" />
      </section>
      <DataTable
        title="Riwayat Pengajuan Cuti"
        subtitle="Data contoh untuk menjelaskan alur request dan approval."
        columns={["Nomor", "Karyawan", "Jenis", "Periode", "Status", "Approver"]}
        rows={visibleRows.map((item) => [
          <div><b>{item.id}</b><span>{item.days} hari kerja</span></div>,
          <div><b>{item.employee}</b><span>Requester</span></div>,
          <div><span>{item.type}</span></div>,
          <div><span>{item.range}</span></div>,
          <div><span className={`badge ${item.status === "Approved" ? "ok" : item.status === "Rejected" ? "bad" : ""}`}>{item.status}</span></div>,
          <div><span>{item.approver}</span></div>,
        ])}
      />
    </>
  );
}

function PayrollView({ user }) {
  const totalThisMonth = payrollRows[0].basic + payrollRows[0].allowance - payrollRows[0].deduction;

  return (
    <>
      <section className="ui-card page-header">
        <div>
          <div className="section-title">Payroll Self Service</div>
          <h1>Slip gaji dan komponen payroll</h1>
          <p>View ini memperlihatkan ringkasan take-home pay, allowance, deduction, dan histori slip gaji bulanan.</p>
        </div>
        <button className="ui-button-secondary" type="button">Download Slip</button>
      </section>
      <section className="stats-grid">
        <StatCard label="Take Home Pay" value={formatRupiah(totalThisMonth)} tone="success" />
        <StatCard label="Allowance" value={formatRupiah(payrollRows[0].allowance)} tone="primary" />
        <StatCard label="Deduction" value={formatRupiah(payrollRows[0].deduction)} tone="warning" />
      </section>
      <section className="two-grid">
        <DataTable
          title="Histori Slip Gaji"
          subtitle={`Pemilik slip: ${user.name}`}
          columns={["Periode", "Gaji Pokok", "Allowance", "Deduction", "Status"]}
          rows={payrollRows.map((item) => [
            <div><b>{item.period}</b></div>,
            <div><span>{formatRupiah(item.basic)}</span></div>,
            <div><span>{formatRupiah(item.allowance)}</span></div>,
            <div><span>{formatRupiah(item.deduction)}</span></div>,
            <div><span className={`badge ${item.status === "Paid" ? "ok" : ""}`}>{item.status}</span></div>,
          ])}
        />
        <div className="ui-card panel payroll-breakdown">
          <div className="panel-head"><div><div className="section-title">April 2026</div><h2>Payroll Breakdown</h2></div></div>
          <p><span>Basic Salary</span><b>{formatRupiah(payrollRows[0].basic)}</b></p>
          <p><span>Transport Allowance</span><b>{formatRupiah(750000)}</b></p>
          <p><span>Meal Allowance</span><b>{formatRupiah(500000)}</b></p>
          <p><span>BPJS & Tax</span><b>-{formatRupiah(payrollRows[0].deduction)}</b></p>
          <div className="payroll-total"><span>Total diterima</span><b>{formatRupiah(totalThisMonth)}</b></div>
        </div>
      </section>
    </>
  );
}

function DirectoryView({ state }) {
  const employees = state.users.filter((item) => item.role === "employee");

  return (
    <>
      <section className="ui-card page-header">
        <div>
          <div className="section-title">Company Directory</div>
          <h1>Direktori karyawan dan struktur tim</h1>
          <p>Menampilkan kontak internal, departemen, posisi, cabang, dan status aktif untuk kebutuhan kolaborasi.</p>
        </div>
      </section>
      <section className="directory-grid">
        {employees.map((employee) => (
          <article className="ui-card directory-card" key={employee.id}>
            <Avatar name={employee.name} />
            <div>
              <h2>{employee.name}</h2>
              <p>{employee.position}</p>
              <span>{employee.department} • {employee.branch}</span>
            </div>
            <div className="directory-actions">
              <button type="button">Email</button>
              <button type="button">Detail</button>
            </div>
          </article>
        ))}
      </section>
    </>
  );
}

function ApprovalsView() {
  const waiting = approvals.filter((item) => item.status !== "Approved").length;

  return (
    <>
      <section className="ui-card page-header">
        <div>
          <div className="section-title">Approval Center</div>
          <h1>Pusat keputusan HR dan finance</h1>
          <p>Admin dapat melihat request cuti, koreksi absensi, reimbursement, dan payroll exception dari satu tempat.</p>
        </div>
        <button className="ui-button-primary" type="button">Review Berikutnya</button>
      </section>
      <section className="stats-grid">
        <StatCard label="Menunggu Review" value={waiting} tone="warning" />
        <StatCard label="SLA Tertua" value="3 hari" />
        <StatCard label="Queue Hari Ini" value={approvals.length} tone="primary" />
      </section>
      <DataTable
        title="Approval Queue"
        subtitle="Simulasi antrean keputusan admin."
        columns={["ID", "Modul", "Requester", "Detail", "Status", "Aksi"]}
        rows={approvals.map((item) => [
          <div><b>{item.id}</b><span>{item.age}</span></div>,
          <div><span>{item.module}</span></div>,
          <div><b>{item.requester}</b></div>,
          <div><span>{item.detail}</span></div>,
          <div><span className="badge">{item.status}</span></div>,
          <div className="row-actions"><button>Approve</button><button>Reject</button></div>,
        ])}
      />
    </>
  );
}

function ProfileView({ user }) {
  return (
    <>
      <section className="ui-card page-header">
        <div>
          <div className="section-title">Employee Profile</div>
          <h1>Profil dan data personal</h1>
          <p>Menampilkan identitas karyawan, dokumen, kontak darurat, dan status face enrollment dalam satu halaman.</p>
        </div>
        <button className="ui-button-secondary" type="button">Edit Profil</button>
      </section>
      <section className="two-grid">
        <div className="ui-card panel profile-card">
          <Avatar name={user.name} />
          <h2>{user.name}</h2>
          <p>{user.position || "HR Manager"}</p>
          <div className="profile-lines">
            <span>Email <b>{user.email}</b></span>
            <span>Kode <b>{user.employeeCode || "ADM-001"}</b></span>
            <span>Departemen <b>{user.department || "People Operations"}</b></span>
            <span>Cabang <b>{user.branch || "Jakarta HQ"}</b></span>
          </div>
        </div>
        <div className="ui-card panel">
          <div className="panel-head"><div><div className="section-title">Documents</div><h2>Kelengkapan Data</h2></div></div>
          {["KTP / Identity", "NPWP / Tax", "Face Reference", "Emergency Contact"].map((item) => (
            <div className="list-row" key={item}><div><b>{item}</b><span>Verified in demo dataset</span></div><span className="badge ok">Complete</span></div>
          ))}
        </div>
      </section>
    </>
  );
}

function DataTable({ title, subtitle, columns, rows }) {
  return (
    <section className="ui-card data-card">
      <div className="data-card-head">
        <div><h2>{title}</h2><p>{subtitle}</p></div>
        <input placeholder="Cari data..." />
      </div>
      <div className="table-wrap">
        <table>
          <thead><tr>{columns.map((column) => <th key={column}>{column}</th>)}</tr></thead>
          <tbody>{rows.map((row, index) => <tr key={index}>{row.map((cell, cellIndex) => <td key={cellIndex}>{cell}</td>)}</tr>)}</tbody>
        </table>
      </div>
    </section>
  );
}

function Attendance({ state, setState, user }) {
  const [gps, setGps] = useState(null);
  const [gpsError, setGpsError] = useState("");
  const [stream, setStream] = useState(null);
  const [photo, setPhoto] = useState("");
  const [cameraType, setCameraType] = useState("");
  const [cameraMessage, setCameraMessage] = useState("Menyiapkan kamera...");
  const [saving, setSaving] = useState(false);
  const videoRef = useRef(null);
  const today = new Date().toISOString().slice(0, 10);
  const myToday = useMemo(() => state.attendances.filter((item) => item.userId === user.id && item.date === today), [state.attendances, user.id, today]);
  const hasIn = myToday.some((item) => item.type === "in");
  const hasOut = myToday.some((item) => item.type === "out");
  const dist = gps ? distanceMeters(gps.lat, gps.lng, state.office.lat, state.office.lng) : null;
  const inOffice = dist != null && dist <= state.office.radiusMeters;

  useEffect(() => {
    navigator.geolocation?.getCurrentPosition(
      (pos) => setGps({ lat: pos.coords.latitude, lng: pos.coords.longitude }),
      (err) => setGpsError(err.message || "Gagal mengambil lokasi.")
    );
  }, []);

  useEffect(() => () => stopCamera(stream), [stream]);

  useEffect(() => {
    if (!cameraType || photo || stream) return;

    let cancelled = false;

    async function bootCamera() {
      setCameraMessage("Meminta akses kamera perangkat...");

      try {
        const nextStream = await startCamera(videoRef.current);

        if (cancelled) {
          stopCamera(nextStream);
          return;
        }

        setStream(nextStream);
        setCameraMessage("Kamera aktif. Pastikan wajah terlihat jelas lalu ambil foto.");
      } catch (error) {
        setCameraMessage(error.message || "Kamera tidak bisa diakses. Pastikan izin kamera diberikan dan tidak sedang dipakai aplikasi lain.");
      }
    }

    const timer = window.setTimeout(bootCamera, 0);

    return () => {
      cancelled = true;
      window.clearTimeout(timer);
    };
  }, [cameraType, photo, stream]);

  function openCamera(type) {
    setPhoto("");
    setCameraType(type);
    setCameraMessage("Menyiapkan kamera...");
  }

  function closeCamera() {
    stopCamera(stream);
    setStream(null);
    setCameraType("");
    setPhoto("");
    setCameraMessage("Menyiapkan kamera...");
  }

  function save(type) {
    if (!gps || !photo || !inOffice) return;
    setSaving(true);
    const now = new Date().toISOString();
    setState((prev) => ({
      ...prev,
      attendances: [{ id: `${user.id}-${type}-${now}`, userId: user.id, userName: user.name, type, date: today, createdAt: now, lat: gps.lat, lng: gps.lng, distanceMeters: Math.round(dist), photo }, ...prev.attendances],
    }));
    setSaving(false);
    closeCamera();
  }

  return (
    <>
      <section className="stats-grid">
        <StatCard label="Status Hari Ini" value={!hasIn ? "Belum Clock-in" : hasOut ? "Presensi selesai" : "Sudah Clock-in"} />
        <StatCard label="Clock-in" value={hasIn ? "Tercatat" : "Belum"} tone={hasIn ? "success" : "warning"} />
        <StatCard label="Clock-out" value={hasOut ? "Tercatat" : "Belum"} tone={hasOut ? "success" : "warning"} />
      </section>

      <section className="ui-card hero-card">
        <div>
          <div className="section-title">Face Attendance</div>
          <h1>Presensi masuk dan pulang dengan verifikasi wajah</h1>
          <p>Demo ini memakai kamera dan GPS asli dari perangkat client. Verifikasi wajah disimulasikan dari hasil capture agar tetap berjalan tanpa backend.</p>
        </div>
        <div className="ui-card-muted location-card">
          <div className="section-title">Status Lokasi</div>
          <p><span>Koordinat Anda</span><b>{gps ? `${fmtCoord(gps.lat)}, ${fmtCoord(gps.lng)}` : "Mendeteksi..."}</b></p>
          <p><span>Lokasi Kantor Terdekat</span><b>{state.office.name}</b></p>
          <p><span>Radius Aktif</span><b>{state.office.radiusMeters} m</b></p>
          <p><span>Jarak Saat Ini</span><b>{dist == null ? "-" : `${Math.round(dist)} m`}</b></p>
          <span className={`badge ${inOffice ? "ok" : "bad"}`}>{inOffice ? "Dalam area kantor" : "Di luar area kantor"}</span>
          {!inOffice && gps ? <button className="sync-button" type="button" onClick={() => setState((prev) => ({ ...prev, office: { ...prev.office, name: "Lokasi Demo - Posisi Client", lat: gps.lat, lng: gps.lng, updatedAt: Date.now() } }))}>Sesuaikan Lokasi Demo ke Posisi Saya</button> : null}
          {gpsError ? <div className="alert danger">{gpsError}</div> : null}
        </div>
      </section>

      <section className="two-grid">
        <ActionCard title="Absensi Masuk" badge="Masuk" disabled={!gps || !inOffice || hasIn} description="Rekam kehadiran awal hari kerja menggunakan kamera perangkat." button={hasIn ? "Clock-in Sudah Tercatat" : "Ambil Foto dan Check-in"} onClick={() => openCamera("in")} />
        <ActionCard title="Absensi Pulang" badge="Pulang" disabled={!gps || !inOffice || !hasIn || hasOut} description="Catat waktu pulang dengan validasi geofence dan identitas wajah yang sama." button={!hasIn ? "Clock-in Dulu" : hasOut ? "Clock-out Sudah Tercatat" : "Ambil Foto dan Check-out"} onClick={() => openCamera("out")} />
      </section>

      <button className="ui-button-secondary reset-button" type="button" onClick={() => setState((prev) => ({ ...prev, attendances: prev.attendances.filter((item) => !(item.userId === user.id && item.date === today)) }))}>Reset Presensi Hari Ini untuk Demo</button>
      <HistoryCard attendances={myToday} emptyText="Belum ada riwayat untuk akun ini." />

      {cameraType ? (
        <div className="modal">
          <div className="capture-card">
            <div className="panel-head"><div><div className="section-title">Capture</div><h2>{cameraType === "in" ? "Konfirmasi Check-in" : "Konfirmasi Check-out"}</h2></div><button className="ui-button-secondary" type="button" onClick={closeCamera}>Tutup</button></div>
            <div className="video-box">{photo ? <img src={photo} alt="Captured attendance" /> : <video ref={videoRef} autoPlay playsInline muted />}</div>
            <div className={`alert ${stream ? "" : "danger"}`}>{photo ? "Wajah terdeteksi. Silakan konfirmasi presensi." : cameraMessage}</div>
            {!photo ? <button className="ui-button-primary full" type="button" disabled={!stream} onClick={() => {
              try {
                setPhoto(captureJpeg(videoRef.current));
              } catch (error) {
                setCameraMessage(error.message || "Kamera belum siap.");
              }
            }}>Ambil Foto</button> : <div className="actions"><button className="ui-button-secondary" type="button" onClick={() => setPhoto("")}>Ulangi Foto</button><button className="ui-button-primary" disabled={saving || !inOffice} type="button" onClick={() => save(cameraType)}>Konfirmasi Absensi</button></div>}
          </div>
        </div>
      ) : null}
    </>
  );
}

function ActionCard({ title, badge, description, button, disabled, onClick }) {
  return (
    <div className="ui-card panel">
      <div className="panel-head"><div><div className="section-title">{badge === "Masuk" ? "Check-in" : "Check-out"}</div><h2>{title}</h2><p>{description}</p></div><span className="badge">{badge}</span></div>
      <button className="ui-button-primary full" type="button" disabled={disabled} onClick={onClick}>{button}</button>
      {disabled ? <div className="alert">Tombol nonaktif karena status hari ini, GPS, atau geofence belum memenuhi syarat.</div> : null}
    </div>
  );
}

function HistoryCard({ attendances, title = "Riwayat Hari Ini", emptyText }) {
  return (
    <div className="ui-card panel">
      <div className="panel-head"><h2>{title}</h2><span className="section-title">Live Log</span></div>
      {attendances.length ? attendances.map((item) => <div className="list-row" key={item.id}><div><b>{item.userName || "Test Employee"}</b><span>{item.type === "in" ? "Check-in" : "Check-out"} • {new Date(item.createdAt).toLocaleString("id-ID")} • {item.distanceMeters}m</span></div><span className={`badge ${item.type}`}>{item.type === "in" ? "Masuk" : "Pulang"}</span></div>) : <div className="empty">{emptyText}</div>}
    </div>
  );
}

function Placeholder({ title }) {
  return (
    <section className="ui-card placeholder">
      <div className="section-title">Demo View</div>
      <h1>{title}</h1>
      <p>View ini dibuat agar client bisa melihat struktur modul dan navigasi seperti aplikasi utama. Untuk demo interaktif penuh, gunakan menu Presensi Wajah.</p>
    </section>
  );
}

function Guide() {
  const demoSteps = [
    ["1", "Login sebagai karyawan", "Gunakan test@gmail.com untuk menunjukkan pengalaman user biasa: dashboard, presensi, cuti, payroll, dan profil."],
    ["2", "Buka Presensi Wajah", "Tunjukkan GPS real, status geofence, tombol sesuaikan lokasi demo, lalu aktifkan kamera dan ambil foto."],
    ["3", "Login sebagai admin", "Gunakan admin@gmail.com untuk membuka executive overview, data karyawan, rekap kehadiran, dan approval center."],
    ["4", "Tutup dengan batasan demo", "Jelaskan bahwa versi ini dibuat untuk eksplorasi cepat di browser, sementara full version memakai backend, database, dan keamanan produksi."],
  ];

  const realFeatures = [
    "Kamera perangkat benar-benar aktif lewat browser permission.",
    "Geolocation memakai koordinat real dari perangkat client.",
    "Geofence bisa disimulasikan ke posisi client agar demo bisa dicoba dari mana saja.",
    "Alur login, navigasi, dashboard, admin, rekap, dan presensi bisa dieksplorasi tanpa server.",
  ];

  const limitations = [
    "Data hanya tersimpan di browser masing-masing melalui client-side storage, bukan database server.",
    "Verifikasi wajah di demo hanya simulasi capture, bukan face matching produksi penuh.",
    "Role, approval, payroll, dan laporan memakai data contoh agar aman untuk portofolio.",
    "Tidak ada email, queue, audit server, enkripsi file, backup database, atau integrasi HR produksi.",
    "Jika browser di-reset atau tombol Reset Demo ditekan, data demo kembali ke kondisi awal.",
  ];

  return (
    <div className="guide-page">
      <section className="ui-card guide-hero">
        <div>
          <div className="section-title">Client Demo Assistant</div>
          <h1>Jalur demo terpandu</h1>
          <p>
            Demo ini dibuat agar client bisa mencoba pengalaman produk langsung dari browser tanpa instalasi server.
            Fokuskan presentasi ke flow utama: login, presensi wajah, GPS/geofence, dashboard karyawan, dan monitoring admin.
          </p>
        </div>
        <div className="limited-card">
          <div className="section-title">Limited Demo</div>
          <h2>Ini bukan full version produksi</h2>
          <p>
            Beberapa fitur sengaja disimulasikan supaya demo aman, gratis, cepat dibuka, dan tidak membutuhkan PHP/backend.
            Full version tetap membutuhkan server, database, storage, security layer, dan konfigurasi deployment produksi.
          </p>
        </div>
      </section>

      <section className="guide-grid">
        <div className="ui-card panel">
          <div className="panel-head">
            <div>
              <div className="section-title">Recommended Flow</div>
              <h2>Urutan presentasi</h2>
            </div>
          </div>
          <div className="guide-steps">
            {demoSteps.map(([number, title, description]) => (
              <article key={number}>
                <span>{number}</span>
                <div>
                  <h3>{title}</h3>
                  <p>{description}</p>
                </div>
              </article>
            ))}
          </div>
        </div>

        <div className="ui-card panel">
          <div className="panel-head">
            <div>
              <div className="section-title">Demo Accounts</div>
              <h2>Akun untuk client</h2>
            </div>
          </div>
          <div className="account-list">
            <div><b>Admin</b><span>admin@gmail.com / password</span></div>
            <div><b>Karyawan</b><span>test@gmail.com / password</span></div>
          </div>
          <div className="alert">
            Saran penyampaian: "Ini demo terbatas untuk eksplorasi UI dan flow. Data yang Bapak/Ibu isi di sini tidak masuk server produksi."
          </div>
        </div>
      </section>

      <section className="guide-grid">
        <Checklist title="Yang benar-benar bisa dicoba" label="Real Browser Features" items={realFeatures} tone="ok" />
        <Checklist title="Batasan demo" label="Not Full Version" items={limitations} tone="warn" />
      </section>

      <section className="ui-card guide-script">
        <div className="section-title">Presenter Script</div>
        <h2>Kalimat singkat ke client</h2>
        <p>
          "Demo ini adalah interactive preview terbatas agar mudah dibuka dari portofolio. Kamera dan lokasi tetap real dari browser,
          tetapi data, payroll, approval, dan face matching produksi dibuat simulasi. Untuk implementasi full version, sistem akan memakai
          server Laravel, database, penyimpanan file, autentikasi produksi, audit log, dan konfigurasi geofence kantor asli."
        </p>
      </section>
    </div>
  );
}

function Checklist({ title, label, items, tone }) {
  return (
    <div className="ui-card panel">
      <div className="panel-head">
        <div>
          <div className="section-title">{label}</div>
          <h2>{title}</h2>
        </div>
      </div>
      <div className="check-list">
        {items.map((item) => (
          <div className={tone} key={item}>
            <span>{tone === "ok" ? "✓" : "!"}</span>
            <p>{item}</p>
          </div>
        ))}
      </div>
    </div>
  );
}

export default function App() {
  const [state, setState] = useDemoState();
  const user = state.currentUser ? state.users.find((item) => item.id === state.currentUser) : null;
  const [page, setPage] = useState(user?.role === "admin" ? "admin" : "dashboard");

  useEffect(() => {
    if (user?.role === "admin" && page === "dashboard") setPage("admin");
  }, [user, page]);

  if (!user) return <Login state={state} setState={setState} />;

  const content = page === "admin" ? <AdminDashboard state={state} /> :
    page === "attendance" ? <Attendance state={state} setState={setState} user={user} /> :
    page === "dashboard" ? <Dashboard state={state} user={user} setPage={setPage} /> :
    page === "guide" ? <Guide /> :
    page === "profile" ? <ProfileView user={user} /> :
    page === "employees" ? <EmployeesView state={state} /> :
    page === "recap" ? <RecapView state={state} /> :
    page === "approvals" ? <ApprovalsView /> :
    page === "leave" ? <LeaveView user={user} /> :
    page === "payroll" ? <PayrollView user={user} /> :
    <DirectoryView state={state} />;

  return (
    <Layout state={state} setState={setState} user={user} page={page} setPage={setPage}>
      <div className="demo-toolbar">
        <span>Interactive preview terbatas: kamera dan geolocation real, data demo berjalan di browser tanpa backend produksi.</span>
        <button type="button" onClick={() => setPage("guide")}>Lihat Batasan Demo</button>
        <button type="button" onClick={() => { resetState(); setState(makeInitialState()); setPage("dashboard"); }}>Reset Demo</button>
      </div>
      {content}
    </Layout>
  );
}
