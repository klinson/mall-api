<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminTablesSeeder::class);
        $this->call(AdminMenuTableSeeder::class);
        $this->call(AdminConfigTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(BroadcastsTableSeeder::class);
        $this->call(GoodsTableSeeder::class);
        $this->call(CategoriesTableSeeder::class);
        $this->call(GoodsSpecificationsTableSeeder::class);
        $this->call(CarouselAdsTableSeeder::class);
        $this->call(FreightTemplatesTableSeeder::class);
        $this->call(AddressesTableSeeder::class);
        $this->call(AgencyConfigsTableSeeder::class);
        $this->call(ExpressesTableSeeder::class);
        $this->call(ArticlesTableSeeder::class);
        $this->call(AreasTableSeeder::class);
        $this->call(MemberLevelsTableSeeder::class);
        $this->call(MemberRechargeActivitiesTableSeeder::class);
        $this->call(PrizesTableSeeder::class);
        $this->call(CouponsTableSeeder::class);
        $this->call(MemberRechargeActivityHasCouponsTableSeeder::class);
        $this->call(DiscountGoodsTableSeeder::class);
        $this->call(StoresTableSeeder::class);
        $this->call(CarouselAdItemsTableSeeder::class);
    }
}
