<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // MASTER
        $this->call(BranchesTableSeeder::class);
        $this->call(DivisionsTableSeeder::class);
        $this->call(DegreesTableSeeder::class);
        $this->call(PositionsTableSeeder::class);
        $this->call(EducationsTableSeeder::class);
        $this->call(EmploymentStatusesTableSeeder::class);
        $this->call(NonTaxableIncomesTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(UnitsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(RoleHasPermissionsTableSeeder::class);
        $this->call(ModelHasRolesTableSeeder::class);
        $this->call(ModelHasPermissionsTableSeeder::class);

        $this->call(EmployeesTableSeeder::class);
        $this->call(EmployeeBanksTableSeeder::class);
        $this->call(EmployeeDocumentsTableSeeder::class);
        $this->call(EmployeeEmergencyContactsTableSeeder::class);
        $this->call(EmployeeFamilyTreesTableSeeder::class);
        $this->call(EmployeeFormalEducationTableSeeder::class);
        $this->call(EmployeeHealthHistoriesTableSeeder::class);
        $this->call(EmployeeInformalEducationTableSeeder::class);
        $this->call(EmployeeInsidersTableSeeder::class);
        $this->call(EmployeeInterestsTableSeeder::class);
        $this->call(EmployeeLanguagesTableSeeder::class);
        $this->call(EmployeeOrganizationsTableSeeder::class);
        $this->call(EmployeePsikotestsTableSeeder::class);
        $this->call(EmployeeReferencesTableSeeder::class);
        $this->call(EmployeeSpecialEducationTableSeeder::class);
        $this->call(EmployeeStrengthWeaknessesTableSeeder::class);
        $this->call(EmployeeWorkExperiencesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(EmployeeRoleHistoriesTableSeeder::class);

        // $this->call(CoasTableSeeder::class);
        $this->call(ItemTypesTableSeeder::class);
        $this->call(ItemCategoriesTableSeeder::class);
        // $this->call(ItemCategoryCoasTableSeeder::class);
        $this->call(ItemsTableSeeder::class);
        $this->call(ItemTypeCoasTableSeeder::class);
        $this->call(ProfitLossCategoriesTableSeeder::class);
        $this->call(ProfitLossSubcategoriesTableSeeder::class);
        $this->call(TaxesTableSeeder::class);
        $this->call(TaxTradingsTableSeeder::class);
        $this->call(BusinessFieldsTableSeeder::class);
        // $this->call(BankInternalsTableSeeder::class);
        // $this->call(BankInternalDetailsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(AssetCategoriesTableSeeder::class);
        // $this->call(ProfitLossDetailsTableSeeder::class);
        // $this->call(PeriodsTableSeeder::class);
        $this->call(VendorsTableSeeder::class);
        $this->call(VendorBanksTableSeeder::class);
        // $this->call(VendorCoasTableSeeder::class);
        $this->call(VendorUsersTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        // $this->call(CustomerBanksTableSeeder::class);
        // $this->call(CustomerCoasTableSeeder::class);
        $this->call(ShNumbersTableSeeder::class);
        $this->call(ShNumberDetailsTableSeeder::class);
        // $this->call(DefaultCoasTableSeeder::class);
        $this->call(ModelsTableSeeder::class);
        $this->call(ModelAuthorizationsTableSeeder::class);
        $this->call(IncomeTaxesTableSeeder::class);
        $this->call(WareHousesTableSeeder::class);
        $this->call(GaragesTableSeeder::class);
        $this->call(MasterGpEvaluationsTableSeeder::class);
        $this->call(MasterLoyaltysTableSeeder::class);
        $this->call(MasterHrdAssessmentsTableSeeder::class);
        $this->call(MasterUserAssessmentsTableSeeder::class);
        // $this->call(LegalityDocumentsTableSeeder::class);
        $this->call(SalaryItemsTableSeeder::class);
        $this->call(AssetDocumentTypesTableSeeder::class);
        // $this->call(PricesTableSeeder::class);
        // $this->call(StockMutationsTableSeeder::class);
        // $this->call(JournalsTableSeeder::class);
        // $this->call(JournalDetailsTableSeeder::class);
        $this->call(CompaniesTableSeeder::class);
        $this->call(EmployeeBranchHistoriesTableSeeder::class);
        $this->call(FeeSalariesTableSeeder::class);
        $this->call(MasterLettersTableSeeder::class);
        $this->call(MasterPrintAuthorizationsTableSeeder::class);
        // $this->call(AssetsTableSeeder::class);
        // $this->call(AssetDocumentsTableSeeder::class);
        // $this->call(FleetsTableSeeder::class);
        // $this->call(MarineFleetsTableSeeder::class);
        // $this->call(VehicleFleetsTableSeeder::class);
        // $this->call(FleetDocumentsTableSeeder::class);
        // $this->call(ItemMinimumsTableSeeder::class);
        // $this->call(LeasesTableSeeder::class);
        // $this->call(LeaseDocumentsTableSeeder::class);
        // $this->call(PriceCustomersTableSeeder::class);
        // $this->call(EduRecruitmentsTableSeeder::class);
        // $this->call(SalariesTableSeeder::class);
    }
}
