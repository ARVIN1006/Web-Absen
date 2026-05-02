const KEY = "corehr_full_demo_no_php_v1";

export function loadState() {
  try {
    const raw = localStorage.getItem(KEY);
    if (!raw) return null;
    return JSON.parse(raw);
  } catch {
    return null;
  }
}

export function saveState(state) {
  localStorage.setItem(KEY, JSON.stringify(state));
}

export function resetState() {
  localStorage.removeItem(KEY);
}

export function makeInitialState() {
  const now = Date.now();
  return {
    version: 1,
    currentUser: null,
    users: [
      { id: "admin", role: "admin", name: "Administrator", email: "admin@gmail.com", password: "password", department: "People Operations", position: "HR Manager", branch: "Jakarta HQ", employeeCode: "ADM-001", active: true },
      { id: "emp", role: "employee", name: "Test Employee", email: "test@gmail.com", password: "password", department: "Engineering", position: "Frontend Developer", branch: "Jakarta HQ", employeeCode: "EMP-014", active: true },
      { id: "emp2", role: "employee", name: "Dinda Prameswari", email: "dinda@gmail.com", password: "password", department: "Finance", position: "Payroll Staff", branch: "Bandung Office", employeeCode: "EMP-021", active: true },
      { id: "emp3", role: "employee", name: "Raka Wijaya", email: "raka@gmail.com", password: "password", department: "Sales", position: "Account Executive", branch: "Surabaya Office", employeeCode: "EMP-027", active: false },
    ],
    office: {
      name: "Kantor Demo",
      lat: -6.2088,
      lng: 106.8456,
      radiusMeters: 150,
      updatedAt: now,
    },
    attendances: [],
  };
}
