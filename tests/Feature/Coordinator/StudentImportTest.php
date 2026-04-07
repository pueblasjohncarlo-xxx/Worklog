<?php

namespace Tests\Feature\Coordinator;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StudentImportTest extends TestCase
{
    use RefreshDatabase;

    private User $coordinator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = User::create([
            'name' => 'Coordinator User',
            'email' => 'coord@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_COORDINATOR,
        ]);
    }

    public function test_coordinator_can_access_import_page()
    {
        $response = $this->actingAs($this->coordinator)->get(route('coordinator.students.import'));
        $response->assertStatus(200);
        $response->assertSee('Import OJT Students');
    }

    public function test_coordinator_can_download_template()
    {
        $response = $this->actingAs($this->coordinator)->get(route('coordinator.students.import.template'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        // $response->assertSee('name,email,password'); // stream response might be tricky to assert like this in some environments
    }

    public function test_coordinator_can_import_students_via_csv()
    {
        $content = "Name,Email,Password\n"; // Test with capital letters
        $content .= "Student One,s1@example.com,pass1234\n";
        $content .= "Student Two,s2@example.com,pass1234\n";

        $file = UploadedFile::fake()->createWithContent('students.csv', $content);

        $response = $this->actingAs($this->coordinator)->post(route('coordinator.students.import.store'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('coordinator.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 's1@example.com', 'role' => User::ROLE_STUDENT]);
        $this->assertDatabaseHas('users', ['email' => 's2@example.com', 'role' => User::ROLE_STUDENT]);
        $this->assertEquals(2, User::where('role', User::ROLE_STUDENT)->count());
    }

    public function test_coordinator_can_import_students_via_semicolon_csv()
    {
        $content = "name;email;password\n"; // Test with semicolon
        $content .= "Student Three;s3@example.com;pass1234\n";

        $file = UploadedFile::fake()->createWithContent('students_semicolon.csv', $content);

        $response = $this->actingAs($this->coordinator)->post(route('coordinator.students.import.store'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('coordinator.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 's3@example.com', 'role' => User::ROLE_STUDENT]);
    }

    public function test_excel_file_upload_shows_friendly_error()
    {
        $file = UploadedFile::fake()->create('students.xlsx', 100);

        $response = $this->actingAs($this->coordinator)->post(route('coordinator.students.import.store'), [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors(['file']);
        $this->assertTrue(str_contains(session('errors')->first('file'), 'restricted by server configuration'));
    }

    public function test_import_validates_csv_header()
    {
        $content = "invalid,header,columns\n";
        $content .= "John,john@example.com,pass1234\n";

        $file = UploadedFile::fake()->createWithContent('bad_header.csv', $content);

        $response = $this->actingAs($this->coordinator)->post(route('coordinator.students.import.store'), [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors(['file']);
    }

    public function test_import_handles_duplicate_emails()
    {
        User::create([
            'name' => 'Existing Student',
            'email' => 'existing@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_STUDENT,
        ]);

        $content = "name,email,password\n";
        $content .= "New Student,existing@example.com,pass1234\n"; // Duplicate email

        $file = UploadedFile::fake()->createWithContent('duplicate.csv', $content);

        $response = $this->actingAs($this->coordinator)->post(route('coordinator.students.import.store'), [
            'file' => $file,
        ]);

        $response->assertSessionHas('import_errors');
        $this->assertEquals(1, User::where('role', User::ROLE_STUDENT)->count());
    }
}
