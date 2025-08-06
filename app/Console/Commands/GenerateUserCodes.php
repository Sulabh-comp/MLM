<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Agency;
use App\Models\Manager;
use App\Models\FamilyMember;
use Illuminate\Console\Command;

class GenerateUserCodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate-codes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate unique codes for existing users (customers, employees, agencies, managers, family members)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to generate codes for existing users...');

        // Generate codes for customers
        $this->info('Generating codes for customers...');
        $customers = Customer::whereNull('code')->get();
        foreach ($customers as $customer) {
            $customer->update(['code' => $customer->generateCode()]);
        }
        $this->info("Generated codes for {$customers->count()} customers.");

        // Generate codes for employees
        $this->info('Generating codes for employees...');
        $employees = Employee::whereNull('code')->get();
        foreach ($employees as $employee) {
            $employee->update(['code' => $employee->generateCode()]);
        }
        $this->info("Generated codes for {$employees->count()} employees.");

        // Generate codes for agencies
        $this->info('Generating codes for agencies...');
        $agencies = Agency::whereNull('code')->get();
        foreach ($agencies as $agency) {
            $agency->update(['code' => $agency->generateCode()]);
        }
        $this->info("Generated codes for {$agencies->count()} agencies.");

        // Generate codes for managers
        $this->info('Generating codes for managers...');
        $managers = Manager::whereNull('code')->get();
        foreach ($managers as $manager) {
            $manager->update(['code' => $manager->generateCode()]);
        }
        $this->info("Generated codes for {$managers->count()} managers.");

        // Generate codes for family members
        $this->info('Generating codes for family members...');
        $familyMembers = FamilyMember::whereNull('code')->get();
        foreach ($familyMembers as $familyMember) {
            $familyMember->update(['code' => $familyMember->generateCode()]);
        }
        $this->info("Generated codes for {$familyMembers->count()} family members.");

        $this->info('All user codes have been generated successfully!');
        
        return Command::SUCCESS;
    }
}
