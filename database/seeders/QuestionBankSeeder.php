<?php

namespace Database\Seeders;

use App\Models\QuestionBank;
use Illuminate\Database\Seeder;

class QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        $questions = $this->getQuestions();
        $adminUser = \App\Models\User::where('email', 'admin@examcraft.com')->first();

        if (!$adminUser) {
            $this->command->error('❌ Admin user not found. Please create an admin user first.');
            return;
        }

        foreach ($questions as $q) {
            QuestionBank::create([
                'user_id' => $adminUser->id,
                'subject' => $q['subject'],
                'grade' => $q['grade'],
                'marks' => 1,
                'data' => $q['data'],
            ]);
        }

        $this->command->info('✅ Question Bank seeded with ' . count($questions) . ' MCQs (user_id: ' . $adminUser->id . ')');
    }

    private function getQuestions(): array
    {
        return [
            // ── O LEVEL PHYSICS ──────────────────────────────────────────────
            ['subject' => 'Physics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the SI unit of force?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Newton', 'image' => null], ['label' => 'B', 'text' => 'Joule', 'image' => null], ['label' => 'C', 'text' => 'Watt', 'image' => null], ['label' => 'D', 'text' => 'Pascal', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Physics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Which of the following is a scalar quantity?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Velocity', 'image' => null], ['label' => 'B', 'text' => 'Speed', 'image' => null], ['label' => 'C', 'text' => 'Acceleration', 'image' => null], ['label' => 'D', 'text' => 'Displacement', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Physics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the speed of light in vacuum?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '3 × 10⁸ m/s', 'image' => null], ['label' => 'B', 'text' => '3 × 10⁶ m/s', 'image' => null], ['label' => 'C', 'text' => '3 × 10¹⁰ m/s', 'image' => null], ['label' => 'D', 'text' => '3 × 10⁴ m/s', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Physics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'The density of water is most nearly:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '100 kg/m³', 'image' => null], ['label' => 'B', 'text' => '500 kg/m³', 'image' => null], ['label' => 'C', 'text' => '1000 kg/m³', 'image' => null], ['label' => 'D', 'text' => '2000 kg/m³', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'Physics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the relationship between energy and frequency?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'E = hf', 'image' => null], ['label' => 'B', 'text' => 'E = fλ', 'image' => null], ['label' => 'C', 'text' => 'E = 1/f', 'image' => null], ['label' => 'D', 'text' => 'E = f²', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Physics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Which type of radiation has the longest wavelength?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Gamma rays', 'image' => null], ['label' => 'B', 'text' => 'Radio waves', 'image' => null], ['label' => 'C', 'text' => 'Ultraviolet', 'image' => null], ['label' => 'D', 'text' => 'X-rays', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Physics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'The resistance of a conductor is inversely proportional to its:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Length', 'image' => null], ['label' => 'B', 'text' => 'Cross-sectional area', 'image' => null], ['label' => 'C', 'text' => 'Temperature', 'image' => null], ['label' => 'D', 'text' => 'Density', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Physics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Which of the following has the highest specific heat capacity?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Iron', 'image' => null], ['label' => 'B', 'text' => 'Copper', 'image' => null], ['label' => 'C', 'text' => 'Water', 'image' => null], ['label' => 'D', 'text' => 'Aluminum', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'Physics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'The angle of incidence equals the angle of reflection is a law of:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Refraction', 'image' => null], ['label' => 'B', 'text' => 'Reflection', 'image' => null], ['label' => 'C', 'text' => 'Diffraction', 'image' => null], ['label' => 'D', 'text' => 'Polarization', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Physics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'The half-life of a radioactive element is the time taken for:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'The element to become stable', 'image' => null], ['label' => 'B', 'text' => 'Half of the nuclei to decay', 'image' => null], ['label' => 'C', 'text' => 'All nuclei to decay', 'image' => null], ['label' => 'D', 'text' => 'The element to fission', 'image' => null]],
                'correct_answer' => 1
            ]],

            // ── O LEVEL CHEMISTRY ────────────────────────────────────────────
            ['subject' => 'Chemistry', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the atomic number of Carbon?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '6', 'image' => null], ['label' => 'B', 'text' => '8', 'image' => null], ['label' => 'C', 'text' => '12', 'image' => null], ['label' => 'D', 'text' => '14', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Chemistry', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Which gas is produced when a metal reacts with dilute acid?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Chlorine', 'image' => null], ['label' => 'B', 'text' => 'Hydrogen', 'image' => null], ['label' => 'C', 'text' => 'Oxygen', 'image' => null], ['label' => 'D', 'text' => 'Nitrogen', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Chemistry', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the pH of a neutral solution?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '0', 'image' => null], ['label' => 'B', 'text' => '7', 'image' => null], ['label' => 'C', 'text' => '14', 'image' => null], ['label' => 'D', 'text' => '1', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Chemistry', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Which element has the symbol Au?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Silver', 'image' => null], ['label' => 'B', 'text' => 'Gold', 'image' => null], ['label' => 'C', 'text' => 'Argon', 'image' => null], ['label' => 'D', 'text' => 'Aluminum', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Chemistry', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the valency of oxygen in most compounds?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '1', 'image' => null], ['label' => 'B', 'text' => '2', 'image' => null], ['label' => 'C', 'text' => '3', 'image' => null], ['label' => 'D', 'text' => '4', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Chemistry', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Which of the following is a covalent compound?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'NaCl', 'image' => null], ['label' => 'B', 'text' => 'CO₂', 'image' => null], ['label' => 'C', 'text' => 'MgO', 'image' => null], ['label' => 'D', 'text' => 'CaF₂', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Chemistry', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the general formula for alkanes?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'CₙH₂ₙ', 'image' => null], ['label' => 'B', 'text' => 'CₙH₂ₙ₊₂', 'image' => null], ['label' => 'C', 'text' => 'CₙHₙ', 'image' => null], ['label' => 'D', 'text' => 'CₙH₂ₙ₋₂', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Chemistry', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Which substance is used as a drying agent in the laboratory?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Sand', 'image' => null], ['label' => 'B', 'text' => 'Concentrated H₂SO₄', 'image' => null], ['label' => 'C', 'text' => 'Water', 'image' => null], ['label' => 'D', 'text' => 'Salt', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Chemistry', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the mass number of an atom?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Number of protons', 'image' => null], ['label' => 'B', 'text' => 'Number of electrons', 'image' => null], ['label' => 'C', 'text' => 'Number of protons + neutrons', 'image' => null], ['label' => 'D', 'text' => 'Number of neutrons', 'image' => null]],
                'correct_answer' => 2
            ]],

            // ── O LEVEL BIOLOGY ─────────────────────────────────────────────
            ['subject' => 'Biology', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Which organelle is known as the powerhouse of the cell?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Nucleus', 'image' => null], ['label' => 'B', 'text' => 'Mitochondrion', 'image' => null], ['label' => 'C', 'text' => 'Chloroplast', 'image' => null], ['label' => 'D', 'text' => 'Ribosome', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Biology', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Photosynthesis primarily occurs in which organelle?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Mitochondrion', 'image' => null], ['label' => 'B', 'text' => 'Nucleus', 'image' => null], ['label' => 'C', 'text' => 'Chloroplast', 'image' => null], ['label' => 'D', 'text' => 'Golgi apparatus', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'Biology', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'How many chambers does a human heart have?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '2', 'image' => null], ['label' => 'B', 'text' => '3', 'image' => null], ['label' => 'C', 'text' => '4', 'image' => null], ['label' => 'D', 'text' => '5', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'Biology', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Which blood cells carry oxygen in mammals?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'White blood cells', 'image' => null], ['label' => 'B', 'text' => 'Red blood cells', 'image' => null], ['label' => 'C', 'text' => 'Platelets', 'image' => null], ['label' => 'D', 'text' => 'Plasma', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Biology', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'The process by which plants absorb water is called:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Transpiration', 'image' => null], ['label' => 'B', 'text' => 'Osmosis', 'image' => null], ['label' => 'C', 'text' => 'Photosynthesis', 'image' => null], ['label' => 'D', 'text' => 'Respiration', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Biology', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Which type of reproduction produces genetically identical offspring?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Sexual reproduction', 'image' => null], ['label' => 'B', 'text' => 'Asexual reproduction', 'image' => null], ['label' => 'C', 'text' => 'Budding and fission', 'image' => null], ['label' => 'D', 'text' => 'Fertilization', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Biology', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the primary function of the kidney?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Produce hormones', 'image' => null], ['label' => 'B', 'text' => 'Filter waste and regulate water', 'image' => null], ['label' => 'C', 'text' => 'Break down food', 'image' => null], ['label' => 'D', 'text' => 'Store oxygen', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Biology', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Which enzyme breaks down starch?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Lipase', 'image' => null], ['label' => 'B', 'text' => 'Protease', 'image' => null], ['label' => 'C', 'text' => 'Amylase', 'image' => null], ['label' => 'D', 'text' => 'Maltase', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'Biology', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'The basic unit of life is the:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Tissue', 'image' => null], ['label' => 'B', 'text' => 'Organ', 'image' => null], ['label' => 'C', 'text' => 'Cell', 'image' => null], ['label' => 'D', 'text' => 'Organism', 'image' => null]],
                'correct_answer' => 2
            ]],

            // ── O LEVEL MATHEMATICS ─────────────────────────────────────────
            ['subject' => 'Mathematics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the value of 2³ × 2²?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '2⁵', 'image' => null], ['label' => 'B', 'text' => '2⁶', 'image' => null], ['label' => 'C', 'text' => '32', 'image' => null], ['label' => 'D', 'text' => '64', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Mathematics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Solve: 3x + 7 = 16',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'x = 3', 'image' => null], ['label' => 'B', 'text' => 'x = 4', 'image' => null], ['label' => 'C', 'text' => 'x = 5', 'image' => null], ['label' => 'D', 'text' => 'x = 6', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Mathematics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the area of a circle with radius 5 cm?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '25π cm²', 'image' => null], ['label' => 'B', 'text' => '10π cm²', 'image' => null], ['label' => 'C', 'text' => '100π cm²', 'image' => null], ['label' => 'D', 'text' => '50π cm²', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Mathematics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the next number in the sequence: 2, 4, 8, 16, ?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '24', 'image' => null], ['label' => 'B', 'text' => '32', 'image' => null], ['label' => 'C', 'text' => '30', 'image' => null], ['label' => 'D', 'text' => '28', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Mathematics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the sum of angles in a triangle?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '90°', 'image' => null], ['label' => 'B', 'text' => '180°', 'image' => null], ['label' => 'C', 'text' => '270°', 'image' => null], ['label' => 'D', 'text' => '360°', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Mathematics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'Express 0.005 in scientific notation:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '5 × 10⁻³', 'image' => null], ['label' => 'B', 'text' => '5 × 10³', 'image' => null], ['label' => 'C', 'text' => '0.5 × 10⁻²', 'image' => null], ['label' => 'D', 'text' => '50 × 10⁻⁴', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Mathematics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the value of √144?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '10', 'image' => null], ['label' => 'B', 'text' => '11', 'image' => null], ['label' => 'C', 'text' => '12', 'image' => null], ['label' => 'D', 'text' => '13', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'Mathematics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is 25% of 80?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '15', 'image' => null], ['label' => 'B', 'text' => '20', 'image' => null], ['label' => 'C', 'text' => '25', 'image' => null], ['label' => 'D', 'text' => '30', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Mathematics', 'grade' => 'O Level', 'data' => [
                'stem_text' => 'What is the perimeter of a rectangle with length 8 cm and width 5 cm?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '13 cm', 'image' => null], ['label' => 'B', 'text' => '26 cm', 'image' => null], ['label' => 'C', 'text' => '40 cm', 'image' => null], ['label' => 'D', 'text' => '20 cm', 'image' => null]],
                'correct_answer' => 1
            ]],

            // ── A LEVEL PHYSICS (10 questions) ──────────────────────────────
            ['subject' => 'Physics', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'What is the relationship between electric field and potential?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'E = V/d', 'image' => null], ['label' => 'B', 'text' => 'E = dV', 'image' => null], ['label' => 'C', 'text' => 'E = V·d', 'image' => null], ['label' => 'D', 'text' => 'E = d/V', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Physics', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'Which equation represents simple harmonic motion?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'F = ma', 'image' => null], ['label' => 'B', 'text' => 'F = -kx', 'image' => null], ['label' => 'C', 'text' => 'F = μN', 'image' => null], ['label' => 'D', 'text' => 'F = mg', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Physics', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'What is the first law of thermodynamics?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Entropy always increases', 'image' => null], ['label' => 'B', 'text' => 'ΔU = Q - W', 'image' => null], ['label' => 'C', 'text' => 'Heat flows from cold to hot', 'image' => null], ['label' => 'D', 'text' => 'P₁V₁ = P₂V₂', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Physics', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'The capacitance of a capacitor is proportional to:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'The voltage applied', 'image' => null], ['label' => 'B', 'text' => 'The plate separation', 'image' => null], ['label' => 'C', 'text' => 'The plate area', 'image' => null], ['label' => 'D', 'text' => 'The charge stored', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'Physics', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'What is the Heisenberg Uncertainty Principle concerned with?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Spin and angular momentum', 'image' => null], ['label' => 'B', 'text' => 'Position and momentum', 'image' => null], ['label' => 'C', 'text' => 'Energy and time', 'image' => null], ['label' => 'D', 'text' => 'Both B and C are correct', 'image' => null]],
                'correct_answer' => 3
            ]],
            ['subject' => 'Physics', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'In a radioactive decay series, which particles can be emitted?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Alpha particles only', 'image' => null], ['label' => 'B', 'text' => 'Beta particles only', 'image' => null], ['label' => 'C', 'text' => 'Alpha, beta, and gamma rays', 'image' => null], ['label' => 'D', 'text' => 'Protons and neutrons', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'Physics', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'What is the energy stored in an inductor?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'E = ½LI²', 'image' => null], ['label' => 'B', 'text' => 'E = LI', 'image' => null], ['label' => 'C', 'text' => 'E = ½L²I', 'image' => null], ['label' => 'D', 'text' => 'E = LI²', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Physics', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'The photoelectric effect occurs because:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Light has wave properties', 'image' => null], ['label' => 'B', 'text' => 'Light has particle properties (photons)', 'image' => null], ['label' => 'C', 'text' => 'Metals absorb all light', 'image' => null], ['label' => 'D', 'text' => 'Electrons repel each other', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Physics', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'What is the relationship in Snell\'s Law?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'n₁ sinθ₁ = n₂ sinθ₂', 'image' => null], ['label' => 'B', 'text' => 'n₁ cosθ₁ = n₂ cosθ₂', 'image' => null], ['label' => 'C', 'text' => 'n₁θ₁ = n₂θ₂', 'image' => null], ['label' => 'D', 'text' => 'n₁/θ₁ = n₂/θ₂', 'image' => null]],
                'correct_answer' => 0
            ]],

            // ── A LEVEL CHEMISTRY (10 questions) ────────────────────────────
            ['subject' => 'Chemistry', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'What is the Avogadro\'s number?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '6.02 × 10²³', 'image' => null], ['label' => 'B', 'text' => '6.02 × 10²⁴', 'image' => null], ['label' => 'C', 'text' => '6.02 × 10²²', 'image' => null], ['label' => 'D', 'text' => '6.02 × 10²⁵', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Chemistry', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'Which type of isomerism involves different spatial arrangements?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Chain isomerism', 'image' => null], ['label' => 'B', 'text' => 'Functional group isomerism', 'image' => null], ['label' => 'C', 'text' => 'Stereoisomerism', 'image' => null], ['label' => 'D', 'text' => 'Position isomerism', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'Chemistry', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'What is the oxidation state of chromium in K₂Cr₂O₇?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '+2', 'image' => null], ['label' => 'B', 'text' => '+4', 'image' => null], ['label' => 'C', 'text' => '+6', 'image' => null], ['label' => 'D', 'text' => '+3', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'Chemistry', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'In acid-base titration, what does the titre refer to?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'The concentration of acid', 'image' => null], ['label' => 'B', 'text' => 'The volume of titrant needed', 'image' => null], ['label' => 'C', 'text' => 'The pH of the solution', 'image' => null], ['label' => 'D', 'text' => 'The indicator used', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Chemistry', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'Which compound shows aromaticity?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Benzene', 'image' => null], ['label' => 'B', 'text' => 'Cyclohexane', 'image' => null], ['label' => 'C', 'text' => 'Cyclohexene', 'image' => null], ['label' => 'D', 'text' => 'Ethene', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Chemistry', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'What is the rate constant k in a first-order reaction?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Independent of concentration', 'image' => null], ['label' => 'B', 'text' => 'Depends on initial concentration', 'image' => null], ['label' => 'C', 'text' => 'Proportional to time', 'image' => null], ['label' => 'D', 'text' => 'Inversely proportional to time', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Chemistry', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'What is Le Chatelier\'s Principle?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Reaction rates are always equal', 'image' => null], ['label' => 'B', 'text' => 'System shifts to counteract stress', 'image' => null], ['label' => 'C', 'text' => 'Equilibrium constants are always 1', 'image' => null], ['label' => 'D', 'text' => 'All reactions are reversible', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Chemistry', 'grade' => 'A Level', 'data' => [
                'stem_text' => 'Nitrogen forms ammonia through:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'The Haber process', 'image' => null], ['label' => 'B', 'text' => 'The Contact process', 'image' => null], ['label' => 'C', 'text' => 'The Ostwald process', 'image' => null], ['label' => 'D', 'text' => 'The Solvay process', 'image' => null]],
                'correct_answer' => 0
            ]],

            // ── 8TH GRADE SCIENCE (10 questions) ────────────────────────────
            ['subject' => 'General Science', 'grade' => '8th Grade', 'data' => [
                'stem_text' => 'What is the process by which water changes from liquid to gas?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Condensation', 'image' => null], ['label' => 'B', 'text' => 'Evaporation', 'image' => null], ['label' => 'C', 'text' => 'Freezing', 'image' => null], ['label' => 'D', 'text' => 'Sublimation', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'General Science', 'grade' => '8th Grade', 'data' => [
                'stem_text' => 'Which planet is known as the Red Planet?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Venus', 'image' => null], ['label' => 'B', 'text' => 'Mars', 'image' => null], ['label' => 'C', 'text' => 'Jupiter', 'image' => null], ['label' => 'D', 'text' => 'Saturn', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'General Science', 'grade' => '8th Grade', 'data' => [
                'stem_text' => 'What is the hardest natural substance on Earth?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Graphite', 'image' => null], ['label' => 'B', 'text' => 'Diamond', 'image' => null], ['label' => 'C', 'text' => 'Iron', 'image' => null], ['label' => 'D', 'text' => 'Granite', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'General Science', 'grade' => '8th Grade', 'data' => [
                'stem_text' => 'How many bones are in the adult human body?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '186', 'image' => null], ['label' => 'B', 'text' => '206', 'image' => null], ['label' => 'C', 'text' => '226', 'image' => null], ['label' => 'D', 'text' => '246', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'General Science', 'grade' => '8th Grade', 'data' => [
                'stem_text' => 'Which gas do plants use for photosynthesis?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Oxygen', 'image' => null], ['label' => 'B', 'text' => 'Nitrogen', 'image' => null], ['label' => 'C', 'text' => 'Carbon dioxide', 'image' => null], ['label' => 'D', 'text' => 'Hydrogen', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'General Science', 'grade' => '8th Grade', 'data' => [
                'stem_text' => 'What is the SI unit of electric current?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Volt', 'image' => null], ['label' => 'B', 'text' => 'Ampere', 'image' => null], ['label' => 'C', 'text' => 'Ohm', 'image' => null], ['label' => 'D', 'text' => 'Watt', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'General Science', 'grade' => '8th Grade', 'data' => [
                'stem_text' => 'The Earth rotates on its axis once every:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '12 hours', 'image' => null], ['label' => 'B', 'text' => '24 hours', 'image' => null], ['label' => 'C', 'text' => '365 days', 'image' => null], ['label' => 'D', 'text' => '7 days', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'General Science', 'grade' => '8th Grade', 'data' => [
                'stem_text' => 'What is the smallest unit of matter?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Molecule', 'image' => null], ['label' => 'B', 'text' => 'Atom', 'image' => null], ['label' => 'C', 'text' => 'Electron', 'image' => null], ['label' => 'D', 'text' => 'Quark', 'image' => null]],
                'correct_answer' => 3
            ]],
            ['subject' => 'General Science', 'grade' => '8th Grade', 'data' => [
                'stem_text' => 'Which vitamin is produced by the skin when exposed to sunlight?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Vitamin A', 'image' => null], ['label' => 'B', 'text' => 'Vitamin C', 'image' => null], ['label' => 'C', 'text' => 'Vitamin D', 'image' => null], ['label' => 'D', 'text' => 'Vitamin B12', 'image' => null]],
                'correct_answer' => 2
            ]],

            // ── 9TH GRADE MATHEMATICS (10 questions) ─────────────────────────
            ['subject' => 'Mathematics', 'grade' => '9th Grade', 'data' => [
                'stem_text' => 'Solve: 2x² - 8 = 0',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'x = ±2', 'image' => null], ['label' => 'B', 'text' => 'x = ±4', 'image' => null], ['label' => 'C', 'text' => 'x = ±1', 'image' => null], ['label' => 'D', 'text' => 'x = ±3', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Mathematics', 'grade' => '9th Grade', 'data' => [
                'stem_text' => 'The volume of a sphere is:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '(4/3)πr²', 'image' => null], ['label' => 'B', 'text' => '(4/3)πr³', 'image' => null], ['label' => 'C', 'text' => '4πr²', 'image' => null], ['label' => 'D', 'text' => 'πr²h', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Mathematics', 'grade' => '9th Grade', 'data' => [
                'stem_text' => 'What is the slope of the line y = 3x - 5?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '-5', 'image' => null], ['label' => 'B', 'text' => '3', 'image' => null], ['label' => 'C', 'text' => '5', 'image' => null], ['label' => 'D', 'text' => '-3', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Mathematics', 'grade' => '9th Grade', 'data' => [
                'stem_text' => 'Factorize: x² - 9',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '(x - 3)(x + 3)', 'image' => null], ['label' => 'B', 'text' => '(x - 9)(x + 1)', 'image' => null], ['label' => 'C', 'text' => '(x - 3)(x - 3)', 'image' => null], ['label' => 'D', 'text' => '(x + 9)(x - 1)', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Mathematics', 'grade' => '9th Grade', 'data' => [
                'stem_text' => 'What is the value of sin(90°)?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '0', 'image' => null], ['label' => 'B', 'text' => '0.5', 'image' => null], ['label' => 'C', 'text' => '1', 'image' => null], ['label' => 'D', 'text' => 'undefined', 'image' => null]],
                'correct_answer' => 2
            ]],
            ['subject' => 'Mathematics', 'grade' => '9th Grade', 'data' => [
                'stem_text' => 'If 3x = 27, then x =',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '3', 'image' => null], ['label' => 'B', 'text' => '6', 'image' => null], ['label' => 'C', 'text' => '9', 'image' => null], ['label' => 'D', 'text' => '12', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Mathematics', 'grade' => '9th Grade', 'data' => [
                'stem_text' => 'The diagonal of a square with side 5 cm is:',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '5 cm', 'image' => null], ['label' => 'B', 'text' => '5√2 cm', 'image' => null], ['label' => 'C', 'text' => '10 cm', 'image' => null], ['label' => 'D', 'text' => '5√3 cm', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Mathematics', 'grade' => '9th Grade', 'data' => [
                'stem_text' => 'What is the mean of 2, 4, 6, 8, 10?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '5', 'image' => null], ['label' => 'B', 'text' => '6', 'image' => null], ['label' => 'C', 'text' => '7', 'image' => null], ['label' => 'D', 'text' => '8', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Mathematics', 'grade' => '9th Grade', 'data' => [
                'stem_text' => 'Express as a power: 2 × 2 × 2 × 2',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => '2³', 'image' => null], ['label' => 'B', 'text' => '2⁴', 'image' => null], ['label' => 'C', 'text' => '2⁵', 'image' => null], ['label' => 'D', 'text' => '2⁶', 'image' => null]],
                'correct_answer' => 1
            ]],

            // ── 10TH GRADE COMPUTER SCIENCE (10 questions) ───────────────────
            ['subject' => 'Computer Science', 'grade' => '10th Grade', 'data' => [
                'stem_text' => 'What does HTML stand for?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Hyper Text Markup Language', 'image' => null], ['label' => 'B', 'text' => 'Home Tool Markup Language', 'image' => null], ['label' => 'C', 'text' => 'Hyperlinks and Text Markup Language', 'image' => null], ['label' => 'D', 'text' => 'High Tech Modern Language', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Computer Science', 'grade' => '10th Grade', 'data' => [
                'stem_text' => 'Which data structure uses LIFO principle?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Queue', 'image' => null], ['label' => 'B', 'text' => 'Stack', 'image' => null], ['label' => 'C', 'text' => 'Tree', 'image' => null], ['label' => 'D', 'text' => 'Graph', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Computer Science', 'grade' => '10th Grade', 'data' => [
                'stem_text' => 'What does CSS stand for?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Computer Style Sheets', 'image' => null], ['label' => 'B', 'text' => 'Cascading Style Sheets', 'image' => null], ['label' => 'C', 'text' => 'Creative Style System', 'image' => null], ['label' => 'D', 'text' => 'Coded Style Syntax', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Computer Science', 'grade' => '10th Grade', 'data' => [
                'stem_text' => 'Which of the following is a programming language?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'MySQL', 'image' => null], ['label' => 'B', 'text' => 'Python', 'image' => null], ['label' => 'C', 'text' => 'HTTP', 'image' => null], ['label' => 'D', 'text' => 'FTP', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Computer Science', 'grade' => '10th Grade', 'data' => [
                'stem_text' => 'What is the correct syntax for a Python if statement?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'if (x > 5)', 'image' => null], ['label' => 'B', 'text' => 'if x > 5:', 'image' => null], ['label' => 'C', 'text' => 'if x > 5 then:', 'image' => null], ['label' => 'D', 'text' => 'if {x > 5}', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Computer Science', 'grade' => '10th Grade', 'data' => [
                'stem_text' => 'What does HTTP stand for?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Hyper Text Transfer Protocol', 'image' => null], ['label' => 'B', 'text' => 'High Tech Transfer Process', 'image' => null], ['label' => 'C', 'text' => 'Home Tool Transfer Protocol', 'image' => null], ['label' => 'D', 'text' => 'Hyperlink Text Transportation Protocol', 'image' => null]],
                'correct_answer' => 0
            ]],
            ['subject' => 'Computer Science', 'grade' => '10th Grade', 'data' => [
                'stem_text' => 'Which data structure is used to implement recursion?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Queue', 'image' => null], ['label' => 'B', 'text' => 'Stack', 'image' => null], ['label' => 'C', 'text' => 'Linked List', 'image' => null], ['label' => 'D', 'text' => 'Array', 'image' => null]],
                'correct_answer' => 1
            ]],
            ['subject' => 'Computer Science', 'grade' => '10th Grade', 'data' => [
                'stem_text' => 'What does JSON stand for?',
                'stem_image' => null,
                'options' => [['label' => 'A', 'text' => 'Java Source Object Notation', 'image' => null], ['label' => 'B', 'text' => 'JavaScript Object Notation', 'image' => null], ['label' => 'C', 'text' => 'Java Serialized Object Network', 'image' => null], ['label' => 'D', 'text' => 'JavaScript Object Network', 'image' => null]],
                'correct_answer' => 1
            ]],
        ];
    }
}
