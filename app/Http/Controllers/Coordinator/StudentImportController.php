<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StudentImportController extends Controller
{
    public function show()
    {
        return view('coordinator.students.import');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="student_import_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'email', 'password']);
            fputcsv($file, ['Student One', 'student.one@example.com', 'pass1234']);
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:2048',
        ]);

        // Manually include the library since it's not autoloaded via Composer
        // Check if file exists, if not, try to require without path helper if needed
        if (file_exists(app_path('Library/SimpleXLSX.php'))) {
            require_once app_path('Library/SimpleXLSX.php');
        }

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();
        $data = [];

        if ($extension === 'xlsx' || $extension === 'xls') {
            throw ValidationException::withMessages(['file' => 'Excel uploads are restricted by server configuration. Please upload a CSV file instead.']);
            if (class_exists('Shuchkin\SimpleXLSX')) {
                if ($xlsx = \Shuchkin\SimpleXLSX::parse($path)) {
                    $data = $xlsx->rows();
                } else {
                    throw ValidationException::withMessages(['file' => \Shuchkin\SimpleXLSX::parseError()]);
                }
            } else {
                // Fallback or error if class not found
                throw ValidationException::withMessages(['file' => 'XLSX parser library not found.']);
            }
        } else {
            // CSV Handling
            $handle = fopen($path, 'r');
            $firstLine = fgets($handle);
            fclose($handle);

            $delimiter = ',';
            if (str_contains($firstLine, ';') && ! str_contains($firstLine, ',')) {
                $delimiter = ';';
            }

            if (($handle = fopen($path, 'r')) !== false) {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                    $data[] = $row;
                }
                fclose($handle);
            }
        }

        if (empty($data) || count($data) <= 1) {
            throw ValidationException::withMessages(['file' => 'The file is empty or contains only headers.']);
        }

        $header = array_shift($data);

        // Normalize headers: lowercase, remove spaces, remove special chars
        // Map common variations to standard keys
        $normalizedHeader = [];
        foreach ($header as $h) {
            $clean = trim(strtolower(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h)));
            $clean = str_replace([' ', '_', '-'], '', $clean); // remove spaces, underscores, dashes

            // Map aliases
            if ($clean === 'lastname' || $clean === 'surname' || $clean === 'familyname') {
                $clean = 'lastname';
            }
            if ($clean === 'firstname' || $clean === 'givenname') {
                $clean = 'firstname';
            }
            if ($clean === 'middlename' || $clean === 'middleinitial' || $clean === 'middle') {
                $clean = 'middlename';
            }
            if ($clean === 'emailaddress' || $clean === 'mail') {
                $clean = 'email';
            }
            if ($clean === 'pwd' || $clean === 'pass') {
                $clean = 'password';
            }

            $normalizedHeader[] = $clean;
        }

        $isSimpleFormat = in_array('name', $normalizedHeader, true);
        $requiredFields = $isSimpleFormat ? ['name', 'email', 'password'] : ['lastname', 'firstname', 'email'];
        $optionalFields = $isSimpleFormat ? ['section', 'department'] : ['middlename', 'age', 'gender', 'section', 'department'];

        // Check if required headers exist
        $missingFields = array_diff($requiredFields, $normalizedHeader);
        if (! empty($missingFields)) {
            throw ValidationException::withMessages(['file' => 'Missing required columns: '.implode(', ', $missingFields)]);
        }

        $indices = [];
        foreach (['name', 'email', 'password', 'lastname', 'firstname', 'middlename', 'age', 'gender', 'section', 'department'] as $field) {
            $indices[$field] = array_search($field, $normalizedHeader);
        }

        $importedCount = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }

            // Pad row if it's shorter than header
            if (count($row) < count($header)) {
                $row = array_pad($row, count($header), '');
            }

            $studentData = [
                'name' => $indices['name'] !== false ? trim($row[$indices['name']]) : '',
                'lastname' => $indices['lastname'] !== false ? trim($row[$indices['lastname']]) : '',
                'firstname' => $indices['firstname'] !== false ? trim($row[$indices['firstname']]) : '',
                'middlename' => $indices['middlename'] !== false ? trim($row[$indices['middlename']]) : '',
                'age' => $indices['age'] !== false ? trim($row[$indices['age']]) : '',
                'gender' => $indices['gender'] !== false ? trim($row[$indices['gender']]) : '',
                'section' => $indices['section'] !== false ? trim($row[$indices['section']]) : '',
                'email' => $indices['email'] !== false ? trim($row[$indices['email']]) : '',
                'password' => $indices['password'] !== false ? trim($row[$indices['password']]) : '',
                'department' => $indices['department'] !== false ? trim($row[$indices['department']]) : '',
            ];

            $studentData['department'] = User::normalizeStudentDepartment($studentData['department']);
            $studentData['section'] = User::normalizeStudentSection($studentData['section'], $studentData['department']);

            if ($studentData['department'] === null) {
                $studentData['department'] = User::inferStudentDepartmentFromSection($studentData['section']);
            }

            if ($studentData['section'] === null) {
                $studentData['section'] = User::normalizeStudentSection(null, $studentData['department'])
                    ?? User::STUDENT_SECTION_BSIT_4A;
            }

            // Normalize Gender
            $rawGender = strtolower($studentData['gender']);
            if ($rawGender === 'male' || $rawGender === 'm' || $rawGender === 'boy') {
                $studentData['gender'] = 'Male';
            } elseif ($rawGender === 'female' || $rawGender === 'f' || $rawGender === 'girl') {
                $studentData['gender'] = 'Female';
            } elseif ($rawGender === 'other' || $rawGender === 'o') {
                $studentData['gender'] = 'Other';
            } else {
                // If unknown, default to empty or keep as is (validation will catch if invalid)
                $studentData['gender'] = ucfirst($rawGender);
            }

            $validator = Validator::make($studentData, $isSimpleFormat ? [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|max:255',
                'section' => ['nullable', 'string', Rule::in(User::STUDENT_SECTIONS)],
                'department' => ['nullable', 'string', Rule::in(User::STUDENT_MAJORS)],
            ] : [
                'lastname' => 'required|string|max:255',
                'firstname' => 'required|string|max:255',
                'middlename' => 'nullable|string|max:255',
                'age' => 'nullable|integer|min:16',
                'gender' => 'nullable|string|in:Male,Female,Other',
                'section' => ['nullable', 'string', Rule::in(User::STUDENT_SECTIONS)],
                'email' => 'required|email|max:255|unique:users,email',
                'department' => ['nullable', 'string', Rule::in(User::STUDENT_MAJORS)],
            ]);

            if ($validator->fails()) {
                $errors[] = 'Row '.($index + 2).' ('.($studentData['email'] ?: 'No Email').'): '.implode(', ', $validator->errors()->all());

                continue;
            }

            $fullName = $isSimpleFormat
                ? $studentData['name']
                : $studentData['firstname'].' '.($studentData['middlename'] ? $studentData['middlename'].' ' : '').$studentData['lastname'];
            $password = $isSimpleFormat ? $studentData['password'] : (strtolower($studentData['lastname']).'123');

            User::create([
                'name' => $fullName,
                'lastname' => $isSimpleFormat ? null : $studentData['lastname'],
                'firstname' => $isSimpleFormat ? null : $studentData['firstname'],
                'middlename' => $isSimpleFormat ? null : $studentData['middlename'],
                'age' => $studentData['age'] ?: null,
                'gender' => $studentData['gender'],
                'email' => $studentData['email'],
                'password' => Hash::make($password),
                'role' => User::ROLE_STUDENT,
                'section' => $studentData['section'],
                'department' => $studentData['department'],
                'is_approved' => true, // Imported students are automatically approved
                'status' => 'approved',
                'has_requested_account' => true,
            ]);

            $importedCount++;
        }

        if (count($errors) > 0) {
            return redirect()->back()
                ->with('status', "Imported $importedCount students with ".count($errors).' errors.')
                ->with('import_errors', $errors);
        }

        return redirect()->route('coordinator.dashboard')
            ->with('status', "Successfully imported $importedCount students.");
    }
}
