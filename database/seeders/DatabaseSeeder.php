<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed App Settings
        $this->call(AppSettingSeeder::class);

        // Seed Departments
        $network = Department::create(['name' => 'Network (NOC & Field)']);
        $sales = Department::create(['name' => 'Sales & Marketing']);
        $hrFinance = Department::create(['name' => 'HR & Finance']);

        // Seed Positions
        $nocManager = Position::create([
            'department_id' => $network->id,
            'name' => 'NOC Manager',
            'base_salary' => 8000000,
        ]);
        $nocStaff = Position::create([
            'department_id' => $network->id,
            'name' => 'NOC Staff',
            'base_salary' => 5000000,
        ]);
        $techStaff = Position::create([
            'department_id' => $network->id,
            'name' => 'Field Technician',
            'base_salary' => 4500000,
        ]);
        $hrdStaff = Position::create([
            'department_id' => $hrFinance->id,
            'name' => 'HRD Staff',
            'base_salary' => 5500000,
        ]);
        $finStaff = Position::create([
            'department_id' => $hrFinance->id,
            'name' => 'Finance Staff',
            'base_salary' => 5500000,
        ]);

        // Seed Users for Roles
        // 1. Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@skynet.com',
            'password' => Hash::make('admin123'),
            'role' => 'super_admin',
        ]);

        // 2. HRD
        $hrdUser = User::create([
            'name' => 'HRD Officer',
            'email' => 'hrd@skynet.com',
            'password' => Hash::make('hrd123'),
            'role' => 'hrd',
        ]);
        // HRD also an employee
        Employee::create([
            'user_id' => $hrdUser->id,
            'position_id' => $hrdStaff->id,
            'employee_id_number' => 'EMP-HRD-001',
            'nik' => '1234567890123456',
            'phone' => '08123456789',
            'employment_status' => 'tetap',
            'join_date' => '2025-01-01',
            'leave_quota' => 12,
            'base_salary' => 5500000,
        ]);

        // 3. Finance
        $financeUser = User::create([
            'name' => 'Finance Officer',
            'email' => 'finance@skynet.com',
            'password' => Hash::make('finance123'),
            'role' => 'finance',
        ]);
        Employee::create([
            'user_id' => $financeUser->id,
            'position_id' => $finStaff->id,
            'employee_id_number' => 'EMP-FIN-001',
            'nik' => '1234567890123457',
            'phone' => '08123456788',
            'employment_status' => 'tetap',
            'join_date' => '2025-01-01',
            'leave_quota' => 12,
            'base_salary' => 5500000,
        ]);

        // 4. Manager (NOC Manager)
        $managerUser = User::create([
            'name' => 'NOC Manager',
            'email' => 'manager@skynet.com',
            'password' => Hash::make('manager123'),
            'role' => 'manager',
        ]);
        Employee::create([
            'user_id' => $managerUser->id,
            'position_id' => $nocManager->id,
            'employee_id_number' => 'EMP-MGR-001',
            'nik' => '1234567890123458',
            'phone' => '08123456787',
            'employment_status' => 'tetap',
            'join_date' => '2025-01-01',
            'leave_quota' => 12,
            'base_salary' => 8000000,
        ]);

        // 5. Employee (NOC Staff)
        $employeeUser = User::create([
            'name' => 'NOC Staff User',
            'email' => 'employee@skynet.com',
            'password' => Hash::make('employee123'),
            'role' => 'employee',
        ]);
        Employee::create([
            'user_id' => $employeeUser->id,
            'position_id' => $nocStaff->id,
            'employee_id_number' => 'EMP-NOC-001',
            'nik' => '1234567890123459',
            'phone' => '08123456786',
            'employment_status' => 'kontrak',
            'join_date' => '2026-01-01',
            'leave_quota' => 12,
            'base_salary' => 5000000,
        ]);

        // 6. Seed Applicants (Pelamar) untuk Uji Coba Berkas
        $nikPelamar = '3273012345678901';
        $targetDir = storage_path("app/public/berkas/{$nikPelamar}");

        if (! file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $sourceFile = public_path('images/test.png');
        $files = [
            'ktp' => 'ktp.png',
            'kk' => 'kk.png',
            'ijazah' => 'ijazah.png',
            'skck' => 'skck.png',
            'pas_foto' => 'pas_foto.png',
            'cv' => 'cv.png',
            'sertifikat' => 'sertifikat.png',
            'sim' => 'sim.png',
        ];

        $documents = [];
        foreach ($files as $key => $filename) {
            $dest = "{$targetDir}/{$filename}";
            if (file_exists($sourceFile)) {
                copy($sourceFile, $dest);
            }
            $documents[$key] = "berkas/{$nikPelamar}/{$filename}";
        }

        // Dokumen pendukung (array)
        $destPendukung = "{$targetDir}/pendukung_1.png";
        if (file_exists($sourceFile)) {
            copy($sourceFile, $destPendukung);
        }
        $documents['pendukung'] = ["berkas/{$nikPelamar}/pendukung_1.png"];

        Applicant::create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'phone' => '082112345678',
            'nik' => $nikPelamar,
            'documents' => $documents,
            'status' => 'pending',
            'metadata' => [
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1998-05-12',
                'alamat' => 'Jl. Merdeka No. 45, Bandung',
                'jenis_kelamin' => 'Laki-laki',
            ],
        ]);

        // Pelamar 2 (Accepted, siap onboarding)
        $nikPelamar2 = '3273012345678902';
        $targetDir2 = storage_path("app/public/berkas/{$nikPelamar2}");
        if (! file_exists($targetDir2)) {
            mkdir($targetDir2, 0755, true);
        }
        $documents2 = [];
        foreach ($files as $key => $filename) {
            $dest = "{$targetDir2}/{$filename}";
            if (file_exists($sourceFile)) {
                copy($sourceFile, $dest);
            }
            $documents2[$key] = "berkas/{$nikPelamar2}/{$filename}";
        }
        $destPendukung2 = "{$targetDir2}/pendukung_1.png";
        if (file_exists($sourceFile)) {
            copy($sourceFile, $destPendukung2);
        }
        $documents2['pendukung'] = ["berkas/{$nikPelamar2}/pendukung_1.png"];

        Applicant::create([
            'name' => 'Siti Aminah',
            'email' => 'siti.aminah@example.com',
            'phone' => '082187654321',
            'nik' => $nikPelamar2,
            'documents' => $documents2,
            'status' => 'accepted',
            'metadata' => [
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2000-08-20',
                'alamat' => 'Jl. Sudirman No. 12, Jakarta',
                'jenis_kelamin' => 'Perempuan',
            ],
        ]);
    }
}
