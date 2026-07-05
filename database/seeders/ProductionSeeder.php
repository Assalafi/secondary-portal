<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Attendance;
use App\Models\ClassArm;
use App\Models\ClassSubject;
use App\Models\ParentGuardian;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Score;
use App\Models\ScoreBatch;
use App\Models\SessionTerm;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\Timetable;
use App\Models\User;
use App\Models\AssessmentSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductionSeeder extends Seeder
{
    // Nigerian surnames
    private $surnames = [
        'Adeyemi', 'Okafor', 'Ibrahim', 'Balogun', 'Okonkwo', 'Abubakar', 'Oluwole', 'Nnamdi',
        'Ogundimu', 'Eze', 'Mohammed', 'Adebayo', 'Chukwu', 'Yusuf', 'Olayinka', 'Emeka',
        'Abdullahi', 'Fashola', 'Nwankwo', 'Suleiman', 'Adekunle', 'Okoro', 'Aliyu', 'Odunayo',
        'Igwe', 'Musa', 'Oladipo', 'Nnadi', 'Garba', 'Owolabi', 'Uzoma', 'Ismail',
        'Akinyemi', 'Obiora', 'Lawal', 'Ezeilo', 'Danladi', 'Afolabi', 'Onwueme', 'Hassan',
        'Bamidele', 'Achebe', 'Tanko', 'Ogundipe', 'Obi', 'Aminu', 'Adeleke', 'Nwosu',
        'Yakubu', 'Ologundudu', 'Okeke', 'Bello', 'Ayodele', 'Ezekwesili', 'Shehu', 'Adeniran',
    ];

    // Nigerian male first names
    private $maleFirstNames = [
        'Oluwaseun', 'Chinedu', 'Abdulrahman', 'Tunde', 'Emeka', 'Usman', 'Kehinde', 'Obinna',
        'Yusuf', 'Adebayo', 'Ikechukwu', 'Musa', 'Olalekan', 'Nnamdi', 'Ibrahim', 'Segun',
        'Chukwuemeka', 'Aliyu', 'Damilola', 'Uchenna', 'Bashir', 'Femi', 'Chidera', 'Abubakar',
        'Temitope', 'Ifeanyi', 'Salisu', 'Kayode', 'Chinonso', 'Suleiman', 'Rotimi', 'Ebuka',
        'Hamza', 'Oluwatobi', 'Kelechi', 'Kabiru', 'Ayodeji', 'Somtochukwu', 'Rilwanu', 'Babatunde',
        'David', 'Samuel', 'Daniel', 'Peter', 'Emmanuel', 'Joshua', 'Solomon', 'Nathaniel',
    ];

    // Nigerian female first names
    private $femaleFirstNames = [
        'Aisha', 'Ngozi', 'Fatimah', 'Oluwabunmi', 'Chidinma', 'Halimah', 'Adenike', 'Nneka',
        'Zainab', 'Folashade', 'Amara', 'Hafsat', 'Titilayo', 'Chiamaka', 'Maryam', 'Omolola',
        'Ifeoma', 'Rahma', 'Morenike', 'Adaeze', 'Bilkisu', 'Yetunde', 'Onyinyechi', 'Salamatu',
        'Bukola', 'Chioma', 'Khadijat', 'Funmilayo', 'Nneamaka', 'Amina', 'Tolulope', 'Ebere',
        'Hadiza', 'Oluwadamilola', 'Chinelo', 'Rukayya', 'Adaora', 'Blessing', 'Grace', 'Faith',
        'Joy', 'Mercy', 'Esther', 'Ruth', 'Deborah', 'Hannah', 'Sarah', 'Priscilla',
    ];

    // Nigerian middle names
    private $middleNames = [
        'Oluwaseun', 'Chukwuemeka', 'Abdulkadir', 'Oluwatosin', 'Nkechi', 'Aminu', 'Olufunmilayo',
        'Ugochukwu', 'Garba', 'Adeola', 'Chibueze', 'Sadiq', 'Olawale', 'Nnenna', 'Haruna',
        'Adeyinka', 'Chisom', 'Isah', 'Opeyemi', 'Obioma', 'Danjuma', 'Temiloluwa', 'Onyeka',
    ];

    // Nigerian states
    private $states = [
        'Lagos', 'Kano', 'Oyo', 'Rivers', 'Kaduna', 'Ogun', 'Anambra', 'Borno', 'Delta',
        'Enugu', 'Imo', 'Kwara', 'Edo', 'Plateau', 'Abia', 'Osun', 'Bauchi', 'Akwa Ibom',
        'Ondo', 'Ekiti', 'Niger', 'Benue', 'Cross River', 'Sokoto', 'Adamawa',
        'Kogi', 'Nassarawa', 'Zamfara', 'FCT Abuja', 'Taraba', 'Ebonyi', 'Kebbi',
    ];

    // LGAs mapped to some states
    private $lgas = [
        'Lagos' => ['Ikeja', 'Surulere', 'Lagos Island', 'Alimosho', 'Eti-Osa', 'Kosofe'],
        'Kano' => ['Kano Municipal', 'Nassarawa', 'Tarauni', 'Fagge', 'Dala', 'Gwale'],
        'Oyo' => ['Ibadan North', 'Ibadan South', 'Ogbomosho', 'Oyo East', 'Iseyin', 'Saki West'],
        'Rivers' => ['Port Harcourt', 'Obio/Akpor', 'Eleme', 'Okrika', 'Bonny', 'Oyigbo'],
        'Kaduna' => ['Kaduna North', 'Kaduna South', 'Zaria', 'Chikun', 'Igabi', 'Soba'],
        'Anambra' => ['Awka South', 'Onitsha', 'Nnewi North', 'Aguata', 'Idemili', 'Anaocha'],
        'FCT Abuja' => ['Abuja Municipal', 'Gwagwalada', 'Kuje', 'Bwari', 'Abaji', 'Kwali'],
    ];

    // Occupations
    private $occupations = [
        'Civil Servant', 'Engineer', 'Medical Doctor', 'Lawyer', 'Businessman',
        'Banker', 'Teacher', 'Pharmacist', 'Architect', 'Accountant',
        'Journalist', 'Pilot', 'Nurse', 'IT Consultant', 'Estate Agent',
        'Trader', 'Contractor', 'Police Officer', 'Military Officer', 'Pastor',
    ];

    // Addresses in Nigerian style
    private $streets = [
        'Adeniran Ogunsanya', 'Obafemi Awolowo Way', 'Herbert Macaulay', 'Murtala Mohammed',
        'Nnamdi Azikiwe', 'Tafawa Balewa', 'Ahmadu Bello Way', 'Yakubu Gowon',
        'Ibrahim Babangida', 'Shehu Shagari Way', 'Awolowo Road', 'Allen Avenue',
        'Adeola Odeku', 'Kofo Abayomi', 'Bourdillon Road', 'Ozumba Mbadiwe',
    ];

    private $areas = [
        'Victoria Island', 'Ikoyi', 'Lekki', 'Ikeja', 'Surulere', 'Yaba',
        'Apapa', 'Maryland', 'Magodo', 'Gbagada', 'Ojodu', 'Ogba',
        'Agege', 'Mushin', 'Oshodi', 'Festac',
    ];

    private function randomDate($start, $end): string
    {
        $startTs = strtotime($start);
        $endTs = strtotime($end);
        $randomTs = rand($startTs, $endTs);
        return date('Y-m-d', $randomTs);
    }

    public function run(): void
    {
        // ========================================
        // 1. ACADEMIC SESSION & TERMS
        // ========================================
        $this->command->info('Creating Academic Sessions & Terms...');
        
        // Create Terms
        $terms = [];
        $termData = [
            ['name' => '1st Term', 'number' => 1],
            ['name' => '2nd Term', 'number' => 2],
            ['name' => '3rd Term', 'number' => 3],
        ];
        foreach ($termData as $t) {
            $terms[] = Term::firstOrCreate(['number' => $t['number']], $t);
        }

        // Create Academic Session
        $session = AcademicSession::firstOrCreate(
            ['name' => '2024/2025'],
            [
                'start_date' => '2024-09-09',
                'end_date' => '2025-07-18',
                'is_current' => true,
            ]
        );

        // Create Session Terms
        $sessionTerms = [];
        $sessionTermData = [
            ['academic_year' => '2024/2025', 'term_name' => '1st Term', 'start_date' => '2024-09-09', 'end_date' => '2024-12-13', 'is_current' => false, 'status' => 'Inactive'],
            ['academic_year' => '2024/2025', 'term_name' => '2nd Term', 'start_date' => '2025-01-06', 'end_date' => '2025-04-04', 'is_current' => false, 'status' => 'Inactive'],
            ['academic_year' => '2024/2025', 'term_name' => '3rd Term', 'start_date' => '2025-04-28', 'end_date' => '2025-07-18', 'is_current' => true, 'status' => 'Active'],
        ];
        foreach ($sessionTermData as $st) {
            $sessionTerms[] = SessionTerm::firstOrCreate(
                ['academic_year' => $st['academic_year'], 'term_name' => $st['term_name']],
                $st
            );
        }

        // ========================================
        // 2. SUBJECTS
        // ========================================
        $this->command->info('Creating Subjects...');
        
        $subjectData = [
            ['name' => 'English Language', 'code' => 'ENG', 'type' => 'Core'],
            ['name' => 'Mathematics', 'code' => 'MTH', 'type' => 'Core'],
            ['name' => 'Civic Education', 'code' => 'CVE', 'type' => 'Core'],
            ['name' => 'Biology', 'code' => 'BIO', 'type' => 'Core'],
            ['name' => 'Chemistry', 'code' => 'CHM', 'type' => 'Core'],
            ['name' => 'Physics', 'code' => 'PHY', 'type' => 'Core'],
            ['name' => 'Further Mathematics', 'code' => 'FMT', 'type' => 'Elective'],
            ['name' => 'Agricultural Science', 'code' => 'AGR', 'type' => 'Elective'],
            ['name' => 'Economics', 'code' => 'ECO', 'type' => 'Elective'],
            ['name' => 'Commerce', 'code' => 'COM', 'type' => 'Elective'],
            ['name' => 'Accounting', 'code' => 'ACC', 'type' => 'Elective'],
            ['name' => 'Government', 'code' => 'GOV', 'type' => 'Elective'],
            ['name' => 'Literature in English', 'code' => 'LIT', 'type' => 'Elective'],
            ['name' => 'Christian Religious Studies', 'code' => 'CRS', 'type' => 'Elective'],
            ['name' => 'Islamic Religious Studies', 'code' => 'IRS', 'type' => 'Elective'],
            ['name' => 'History', 'code' => 'HIS', 'type' => 'Elective'],
            ['name' => 'Geography', 'code' => 'GEO', 'type' => 'Elective'],
            ['name' => 'Computer Studies', 'code' => 'ICT', 'type' => 'Core'],
            ['name' => 'Physical & Health Education', 'code' => 'PHE', 'type' => 'Core'],
            ['name' => 'Basic Technology', 'code' => 'BTH', 'type' => 'Core'],
            ['name' => 'Technical Drawing', 'code' => 'TDR', 'type' => 'Elective'],
            ['name' => 'French', 'code' => 'FRN', 'type' => 'Elective'],
        ];

        $subjects = [];
        foreach ($subjectData as $s) {
            $subjects[$s['code']] = Subject::firstOrCreate(['code' => $s['code']], $s);
        }

        // ========================================
        // 3. CLASSES & ARMS
        // ========================================
        $this->command->info('Creating Classes & Arms...');
        
        $classData = [
            ['level' => 'JSS', 'name' => 'JSS 1', 'numeric_level' => 1, 'group' => 'Junior', 'status' => 'Active'],
            ['level' => 'JSS', 'name' => 'JSS 2', 'numeric_level' => 2, 'group' => 'Junior', 'status' => 'Active'],
            ['level' => 'JSS', 'name' => 'JSS 3', 'numeric_level' => 3, 'group' => 'Junior', 'status' => 'Active'],
            ['level' => 'SS', 'name' => 'SS 1', 'numeric_level' => 4, 'group' => 'Senior', 'status' => 'Active'],
            ['level' => 'SS', 'name' => 'SS 2', 'numeric_level' => 5, 'group' => 'Senior', 'status' => 'Active'],
            ['level' => 'SS', 'name' => 'SS 3', 'numeric_level' => 6, 'group' => 'Senior', 'status' => 'Active'],
        ];

        $classes = [];
        foreach ($classData as $c) {
            $classes[$c['name']] = SchoolClass::firstOrCreate(['name' => $c['name']], $c);
        }

        // Create class arms (A, B for each class)
        $classArms = [];
        $armNames = ['A', 'B'];
        foreach ($classes as $level => $class) {
            foreach ($armNames as $arm) {
                $classArms[$level . ' ' . $arm] = ClassArm::firstOrCreate(
                    ['school_class_id' => $class->id, 'name' => $arm],
                    ['school_class_id' => $class->id, 'name' => $arm, 'capacity' => 40]
                );
            }
        }

        // ========================================
        // 4. ROLES (ensure they exist)
        // ========================================
        $this->command->info('Ensuring Roles exist...');
        $roleSeeder = new RoleSeeder();
        $roleSeeder->run();

        $teacherRole = Role::where('name', 'Teacher')->first();
        $studentRole = Role::where('name', 'Student')->first();
        $parentRole = Role::where('name', 'Parent')->first();

        // ========================================
        // 5. TEACHERS (15 teachers)
        // ========================================
        $this->command->info('Creating Teachers...');
        
        $teacherData = [
            ['name' => 'Mr. Adeniyi Ogundimu', 'gender' => 'Male', 'email' => 'a.ogundimu@school.com', 'designation' => 'Senior Teacher', 'department' => 'Sciences', 'qualifications' => 'B.Sc Physics, M.Ed', 'state' => 'Lagos'],
            ['name' => 'Mrs. Ngozi Okafor', 'gender' => 'Female', 'email' => 'n.okafor@school.com', 'designation' => 'Senior Teacher', 'department' => 'Sciences', 'qualifications' => 'B.Sc Chemistry, PGDE', 'state' => 'Anambra'],
            ['name' => 'Mr. Ibrahim Suleiman', 'gender' => 'Male', 'email' => 'i.suleiman@school.com', 'designation' => 'Teacher', 'department' => 'Mathematics', 'qualifications' => 'B.Sc Mathematics, NCE', 'state' => 'Kano'],
            ['name' => 'Mrs. Folashade Adebayo', 'gender' => 'Female', 'email' => 'f.adebayo@school.com', 'designation' => 'Senior Teacher', 'department' => 'English', 'qualifications' => 'B.A English, M.A Literature', 'state' => 'Oyo'],
            ['name' => 'Mr. Emeka Nwankwo', 'gender' => 'Male', 'email' => 'e.nwankwo@school.com', 'designation' => 'Teacher', 'department' => 'Social Sciences', 'qualifications' => 'B.Sc Economics, PGDE', 'state' => 'Imo'],
            ['name' => 'Mrs. Halimah Garba', 'gender' => 'Female', 'email' => 'h.garba@school.com', 'designation' => 'Teacher', 'department' => 'Arts', 'qualifications' => 'B.A History, NCE', 'state' => 'Kaduna'],
            ['name' => 'Mr. Oluwaseun Afolabi', 'gender' => 'Male', 'email' => 'o.afolabi@school.com', 'designation' => 'Teacher', 'department' => 'Sciences', 'qualifications' => 'B.Sc Biology, PGDE', 'state' => 'Osun'],
            ['name' => 'Mrs. Chidinma Eze', 'gender' => 'Female', 'email' => 'c.eze@school.com', 'designation' => 'Teacher', 'department' => 'Commercial', 'qualifications' => 'B.Sc Accounting, NCE', 'state' => 'Enugu'],
            ['name' => 'Mr. Musa Abdullahi', 'gender' => 'Male', 'email' => 'm.abdullahi@school.com', 'designation' => 'Teacher', 'department' => 'Arabic/Islamic Studies', 'qualifications' => 'B.A Arabic, PGDE', 'state' => 'Borno'],
            ['name' => 'Mrs. Adenike Bamidele', 'gender' => 'Female', 'email' => 'a.bamidele@school.com', 'designation' => 'Teacher', 'department' => 'Computer Science', 'qualifications' => 'B.Sc Computer Science, PGDE', 'state' => 'Ekiti'],
            ['name' => 'Mr. Yakubu Tanko', 'gender' => 'Male', 'email' => 'y.tanko@school.com', 'designation' => 'Teacher', 'department' => 'Physical Education', 'qualifications' => 'B.Sc Physical Education', 'state' => 'Niger'],
            ['name' => 'Mrs. Ifeoma Okoro', 'gender' => 'Female', 'email' => 'i.okoro@school.com', 'designation' => 'Teacher', 'department' => 'Sciences', 'qualifications' => 'B.Sc Agricultural Science', 'state' => 'Abia'],
            ['name' => 'Mr. Femi Owolabi', 'gender' => 'Male', 'email' => 'f.owolabi@school.com', 'designation' => 'Senior Teacher', 'department' => 'Arts', 'qualifications' => 'B.A Government, M.Sc Political Science', 'state' => 'Ogun'],
            ['name' => 'Mrs. Amina Bello', 'gender' => 'Female', 'email' => 'a.bello@school.com', 'designation' => 'Teacher', 'department' => 'Languages', 'qualifications' => 'B.A French, DELF B2', 'state' => 'Sokoto'],
            ['name' => 'Mr. Chinonso Igwe', 'gender' => 'Male', 'email' => 'c.igwe@school.com', 'designation' => 'Teacher', 'department' => 'Sciences', 'qualifications' => 'B.Tech Mechanical Engineering, PGDE', 'state' => 'Delta'],
        ];

        $teachers = [];
        foreach ($teacherData as $index => $td) {
            $user = User::firstOrCreate(
                ['email' => $td['email']],
                [
                    'name' => $td['name'],
                    'email' => $td['email'],
                    'password' => Hash::make('teacher123'),
                    'role_id' => $teacherRole->id,
                    'phone' => '+234-80' . rand(10, 99) . '-' . rand(100, 999) . '-' . rand(1000, 9999),
                    'address' => rand(1, 50) . ' ' . $this->streets[array_rand($this->streets)] . ', ' . $this->areas[array_rand($this->areas)] . ', Lagos',
                    'status' => 'Active',
                    'gender' => $td['gender'],
                    'state_of_origin' => $td['state'],
                    'nationality' => 'Nigerian',
                    'email_verified_at' => now(),
                ]
            );

            $staff = Staff::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'staff_id' => 'TCH/' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    'designation' => $td['designation'],
                    'department' => $td['department'],
                    'date_of_employment' => $this->randomDate('2015-01-01', '2023-08-01'),
                    'salary' => rand(150, 350) * 1000,
                    'qualifications' => $td['qualifications'],
                    'employment_type' => 'Full-time',
                    'status' => 'Active',
                ]
            );

            $teachers[] = $user;
        }

        // Assign class teachers
        $armIndex = 0;
        foreach ($classArms as $key => $arm) {
            if (isset($teachers[$armIndex])) {
                $arm->update(['class_teacher_id' => $teachers[$armIndex]->id]);
            }
            $armIndex++;
        }

        // ========================================
        // 6. ASSIGN SUBJECTS TO CLASSES
        // ========================================
        $this->command->info('Assigning Subjects to Classes...');

        // Junior subjects
        $juniorSubjects = ['ENG', 'MTH', 'CVE', 'BIO', 'CHM', 'PHY', 'AGR', 'GEO', 'HIS', 'ICT', 'PHE', 'BTH', 'CRS', 'FRN'];
        // Senior subjects
        $seniorSubjects = ['ENG', 'MTH', 'CVE', 'BIO', 'CHM', 'PHY', 'FMT', 'ECO', 'COM', 'ACC', 'GOV', 'LIT', 'ICT', 'PHE', 'GEO', 'TDR'];

        // Subject-teacher mapping
        $subjectTeacherMap = [
            'ENG' => 3, 'MTH' => 2, 'CVE' => 12, 'BIO' => 6, 'CHM' => 1, 'PHY' => 0,
            'FMT' => 2, 'AGR' => 11, 'ECO' => 4, 'COM' => 4, 'ACC' => 7, 'GOV' => 12,
            'LIT' => 3, 'CRS' => 5, 'IRS' => 8, 'HIS' => 5, 'GEO' => 12, 'ICT' => 9,
            'PHE' => 10, 'BTH' => 14, 'TDR' => 14, 'FRN' => 13,
        ];

        foreach ($classArms as $key => $arm) {
            $isJunior = str_contains($key, 'JSS');
            $subjectCodes = $isJunior ? $juniorSubjects : $seniorSubjects;

            foreach ($subjectCodes as $code) {
                $subject = $subjects[$code];
                $teacherIndex = $subjectTeacherMap[$code] ?? 0;
                $teacher = $teachers[$teacherIndex];

                ClassSubject::firstOrCreate(
                    ['class_arm_id' => $arm->id, 'subject_id' => $subject->id],
                    ['class_arm_id' => $arm->id, 'subject_id' => $subject->id, 'teacher_id' => $teacher->id]
                );
            }
        }

        // ========================================
        // 7. STUDENTS (120 students - ~10 per arm)
        // ========================================
        $this->command->info('Creating Students...');

        $students = [];
        $studentCounter = 1;

        foreach ($classArms as $key => $arm) {
            $numStudents = rand(9, 12);
            for ($i = 0; $i < $numStudents; $i++) {
                $gender = rand(0, 1) ? 'Male' : 'Female';
                $surname = $this->surnames[array_rand($this->surnames)];
                $firstName = $gender === 'Male'
                    ? $this->maleFirstNames[array_rand($this->maleFirstNames)]
                    : $this->femaleFirstNames[array_rand($this->femaleFirstNames)];
                $middleName = $this->middleNames[array_rand($this->middleNames)];
                $state = $this->states[array_rand($this->states)];
                $lgaOptions = $this->lgas[$state] ?? ['Central', 'North', 'South'];
                $lga = $lgaOptions[array_rand($lgaOptions)];

                $admissionNo = 'SSP/' . date('Y') . '/' . str_pad($studentCounter, 4, '0', STR_PAD_LEFT);

                // Create user for student
                $email = strtolower($firstName[0] . '.' . $surname . $studentCounter . '@student.school.com');
                $studentUser = User::firstOrCreate(
                    ['email' => $email],
                    [
                        'name' => $firstName . ' ' . $surname,
                        'email' => $email,
                        'password' => Hash::make('student123'),
                        'role_id' => $studentRole->id,
                        'phone' => '+234-80' . rand(10, 99) . '-' . rand(100, 999) . '-' . rand(1000, 9999),
                        'address' => rand(1, 100) . ' ' . $this->streets[array_rand($this->streets)] . ', ' . $this->areas[array_rand($this->areas)] . ', Lagos',
                        'status' => 'Active',
                        'gender' => $gender,
                        'state_of_origin' => $state,
                        'nationality' => 'Nigerian',
                        'lga' => $lga,
                        'email_verified_at' => now(),
                    ]
                );

                $dob = $this->randomDate('2007-01-01', '2012-12-31');
                $student = Student::firstOrCreate(
                    ['admission_no' => $admissionNo],
                    [
                        'user_id' => $studentUser->id,
                        'admission_no' => $admissionNo,
                        'admission_date' => $this->randomDate('2020-09-01', '2024-09-01'),
                        'surname' => $surname,
                        'first_name' => $firstName,
                        'middle_name' => $middleName,
                        'gender' => $gender,
                        'dob' => $dob,
                        'place_of_birth_town' => $this->areas[array_rand($this->areas)],
                        'place_of_birth_lga' => $lga,
                        'place_of_birth_state' => $state,
                        'nationality' => 'Nigerian',
                        'state_of_origin' => $state,
                        'lga' => $lga,
                        'health_status' => 'Good',
                        'current_class_arm_id' => $arm->id,
                        'academic_session_id' => $session->id,
                        'status' => 'Active',
                    ]
                );

                $students[] = ['student' => $student, 'user' => $studentUser, 'arm' => $arm];
                $studentCounter++;
            }
        }

        // ========================================
        // 8. PARENTS & LINK TO STUDENTS
        // ========================================
        $this->command->info('Creating Parents & Linking to Students...');

        $parentCounter = 1;
        foreach ($students as $studentData) {
            $student = $studentData['student'];
            $studentUser = $studentData['user'];

            // Create parent (each student gets one parent user)
            $parentGender = rand(0, 1) ? 'Male' : 'Female';
            $parentSurname = $student->surname;
            $parentFirstName = $parentGender === 'Male'
                ? $this->maleFirstNames[array_rand($this->maleFirstNames)]
                : $this->femaleFirstNames[array_rand($this->femaleFirstNames)];

            $parentEmail = strtolower($parentFirstName[0] . '.' . $parentSurname . '.p' . $parentCounter . '@parent.school.com');
            $occupation = $this->occupations[array_rand($this->occupations)];
            $relationship = $parentGender === 'Male' ? 'Father' : 'Mother';

            $parentUser = User::firstOrCreate(
                ['email' => $parentEmail],
                [
                    'name' => ($parentGender === 'Male' ? 'Mr. ' : 'Mrs. ') . $parentFirstName . ' ' . $parentSurname,
                    'email' => $parentEmail,
                    'password' => Hash::make('parent123'),
                    'role_id' => $parentRole->id,
                    'phone' => '+234-80' . rand(10, 99) . '-' . rand(100, 999) . '-' . rand(1000, 9999),
                    'address' => rand(1, 100) . ' ' . $this->streets[array_rand($this->streets)] . ', ' . $this->areas[array_rand($this->areas)] . ', Lagos',
                    'occupation' => $occupation,
                    'status' => 'Active',
                    'gender' => $parentGender,
                    'state_of_origin' => $student->state_of_origin,
                    'nationality' => 'Nigerian',
                    'email_verified_at' => now(),
                ]
            );

            // Create parent_guardian record
            ParentGuardian::firstOrCreate(
                ['user_id' => $parentUser->id],
                [
                    'user_id' => $parentUser->id,
                    'full_name' => $parentUser->name,
                    'relationship_to_student' => $relationship,
                    'occupation' => $occupation,
                    'present_address' => $parentUser->address,
                    'permanent_address' => $parentUser->address,
                    'phone_residence' => $parentUser->phone,
                    'email' => $parentUser->email,
                ]
            );

            // Link parent to student via parent_student pivot
            DB::table('parent_student')->insertOrIgnore([
                'parent_id' => $parentUser->id,
                'student_id' => $student->id,
                'relationship' => $relationship,
                'date_added' => now()->format('Y-m-d'),
                'is_primary' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $parentCounter++;
        }

        // ========================================
        // 9. TIMETABLE
        // ========================================
        $this->command->info('Creating Timetable...');

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $periods = [
            ['08:00:00', '08:45:00'],
            ['08:45:00', '09:30:00'],
            ['09:30:00', '10:15:00'],
            ['10:30:00', '11:15:00'], // after break
            ['11:15:00', '12:00:00'],
            ['12:00:00', '12:45:00'],
            ['13:15:00', '14:00:00'], // after lunch
            ['14:00:00', '14:45:00'],
        ];

        $rooms = ['Room A1', 'Room A2', 'Room B1', 'Room B2', 'Room C1', 'Room C2', 'Lab 1', 'Lab 2', 'ICT Lab', 'Hall 1'];

        foreach ($classArms as $key => $arm) {
            $isJunior = str_contains($key, 'JSS');
            $subjectCodes = $isJunior ? $juniorSubjects : $seniorSubjects;
            $subjectPool = [];
            foreach ($subjectCodes as $code) {
                $subjectPool[] = $subjects[$code];
            }

            $subjectIndex = 0;
            foreach ($days as $day) {
                $periodCount = rand(6, 8);
                for ($p = 0; $p < $periodCount && $p < count($periods); $p++) {
                    $subject = $subjectPool[$subjectIndex % count($subjectPool)];
                    $teacherIndex = $subjectTeacherMap[$subject->code] ?? 0;

                    Timetable::firstOrCreate(
                        ['class_arm_id' => $arm->id, 'day' => $day, 'start_time' => $periods[$p][0]],
                        [
                            'class_arm_id' => $arm->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teachers[$teacherIndex]->id,
                            'day' => $day,
                            'start_time' => $periods[$p][0],
                            'end_time' => $periods[$p][1],
                            'room' => $rooms[array_rand($rooms)],
                            'status' => 'Active',
                        ]
                    );

                    $subjectIndex++;
                }
            }
        }

        // ========================================
        // 10. SCORES (for 3rd Term)
        // ========================================
        $this->command->info('Creating Scores...');

        $term3 = $terms[2]; // 3rd term

        foreach ($classArms as $key => $arm) {
            $isJunior = str_contains($key, 'JSS');
            $subjectCodes = $isJunior ? $juniorSubjects : $seniorSubjects;
            $classId = $arm->schoolClass ? $arm->schoolClass->id : $arm->school_class_id;

            foreach ($subjectCodes as $code) {
                $subject = $subjects[$code];
                $teacherIndex = $subjectTeacherMap[$code] ?? 0;
                $teacher = $teachers[$teacherIndex];

                // Create score batch
                $batch = ScoreBatch::firstOrCreate(
                    ['class_id' => $classId, 'subject_id' => $subject->id, 'academic_session_id' => $session->id, 'term_id' => $term3->id],
                    [
                        'class_id' => $classId,
                        'subject_id' => $subject->id,
                        'academic_session_id' => $session->id,
                        'term_id' => $term3->id,
                        'first_ca_max' => 10,
                        'second_ca_max' => 10,
                        'third_ca_max' => 10,
                        'exam_max' => 70,
                        'status' => 'published',
                        'uploaded_by' => $teacher->id,
                        'uploaded_at' => now(),
                        'published_at' => now(),
                    ]
                );

                // Create scores for students in this arm
                $armStudents = collect($students)->filter(fn($s) => $s['arm']->id === $arm->id);
                foreach ($armStudents as $sd) {
                    $ca1 = rand(4, 10);
                    $ca2 = rand(4, 10);
                    $ca3 = rand(3, 10);
                    $exam = rand(25, 65);
                    $total = $ca1 + $ca2 + $ca3 + $exam;

                    // Determine grade
                    $grade = match(true) {
                        $total >= 75 => 'A',
                        $total >= 65 => 'B',
                        $total >= 55 => 'C',
                        $total >= 45 => 'D',
                        $total >= 40 => 'E',
                        default => 'F',
                    };

                    $remark = match($grade) {
                        'A' => 'Excellent',
                        'B' => 'Very Good',
                        'C' => 'Good',
                        'D' => 'Fair',
                        'E' => 'Pass',
                        'F' => 'Fail',
                    };

                    Score::firstOrCreate(
                        ['score_batch_id' => $batch->id, 'student_id' => $sd['student']->id],
                        [
                            'score_batch_id' => $batch->id,
                            'student_id' => $sd['student']->id,
                            'first_ca' => $ca1,
                            'second_ca' => $ca2,
                            'third_ca' => $ca3,
                            'exam' => $exam,
                            'total' => $total,
                            'grade' => $grade,
                            'remark' => $remark,
                        ]
                    );
                }
            }
        }

        // ========================================
        // 11. ASSESSMENT SCHEDULES
        // ========================================
        $this->command->info('Creating Assessment Schedules...');

        $adminUser = User::where('email', 'admin@school.com')->first() ?? $teachers[0];
        $assessmentTypes = ['Test', 'Exam'];

        foreach ($classes as $level => $class) {
            $isJunior = str_contains($level, 'JSS');
            $subjectCodes = $isJunior ? $juniorSubjects : $seniorSubjects;

            foreach ($assessmentTypes as $type) {
                $subjectSample = array_slice($subjectCodes, 0, 8); // First 8 subjects
                $dateStart = $type === 'Test' ? '2025-06-02' : '2025-07-01';

                foreach ($subjectSample as $idx => $code) {
                    $subject = $subjects[$code];
                    $schedDate = date('Y-m-d', strtotime($dateStart . ' +' . $idx . ' days'));

                    AssessmentSchedule::firstOrCreate(
                        ['class_id' => $class->id, 'subject_id' => $subject->id, 'term_id' => $term3->id, 'assessment_type' => $type],
                        [
                            'assessment_type' => $type,
                            'class_id' => $class->id,
                            'subject_id' => $subject->id,
                            'academic_session_id' => $session->id,
                            'term_id' => $term3->id,
                            'scheduled_date' => $schedDate,
                            'scheduled_time' => '09:00:00',
                            'status' => 'Scheduled',
                            'created_by' => $adminUser ? $adminUser->id : 1,
                        ]
                    );
                }
            }
        }

        // ========================================
        // 12. ATTENDANCE (last 2 weeks)
        // ========================================
        $this->command->info('Creating Attendance Records...');

        $startDate = now()->subDays(14);
        for ($d = 0; $d < 10; $d++) { // 10 school days
            $date = $startDate->copy()->addDays($d);
            if ($date->isWeekend()) continue;

            foreach ($classArms as $key => $arm) {
                $armStudents = collect($students)->filter(fn($s) => $s['arm']->id === $arm->id);
                $teacherIndex = array_search($arm, array_values($classArms));
                $markedBy = $teachers[$teacherIndex % count($teachers)] ?? $teachers[0];

                foreach ($armStudents as $sd) {
                    $status = rand(1, 100) <= 90 ? 'Present' : (rand(0, 1) ? 'Absent' : 'Late');
                    
                    Attendance::firstOrCreate(
                        ['student_id' => $sd['student']->id, 'date' => $date->format('Y-m-d')],
                        [
                            'student_id' => $sd['student']->id,
                            'class_arm_id' => $arm->id,
                            'date' => $date->format('Y-m-d'),
                            'status' => $status,
                            'remarks' => $status === 'Absent' ? 'Absent without excuse' : null,
                            'marked_by' => $markedBy->id,
                        ]
                    );
                }
            }
        }

        $this->command->info('✅ Production seeding completed successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Academic Session: 2024/2025 (3 terms)');
        $this->command->info('   - Subjects: ' . count($subjects));
        $this->command->info('   - Classes: ' . count($classes) . ' (with 2 arms each)');
        $this->command->info('   - Teachers: ' . count($teachers));
        $this->command->info('   - Students: ' . count($students));
        $this->command->info('   - Parents: ' . count($students));
        $this->command->info('   - Timetable entries created');
        $this->command->info('   - Scores for 3rd Term created');
        $this->command->info('   - Assessment schedules created');
        $this->command->info('   - Attendance records (last 2 weeks)');
    }
}
