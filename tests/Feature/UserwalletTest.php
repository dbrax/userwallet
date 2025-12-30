<?php

namespace Epmnzava\Userwallet\Tests\Feature;


/**
 * Author: Emmanuel Paul Mnzava
 * Twitter: @epmnzava
 * Github:https://github.com/dbrax/userwallet
 * Email: epmnzava@gmail.com
 * 
 */


use Illuminate\Foundation\Testing\RefreshDatabase;
use Epmnzava\Userwallet\Userwallet;
use Epmnzava\Userwallet\Models\Wallet;
use Epmnzava\Userwallet\Models\WalletLedger;
use Epmnzava\Userwallet\Tests\TestCase;

class UserwalletTest extends TestCase
{
    use RefreshDatabase;

    protected Userwallet $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new Userwallet();
    }

    /** @test */
    public function deposit_creates_wallet_and_ledger_when_user_has_no_wallet()
    {
        $userId = 10;

        $res = $this->service->deposit(
            userid: $userId,
            amount: "100.50",
            source: "cash",
            note: "initial",
            receipt: "R001",
            destination: "main"
        );

        $this->assertEquals(1, $res['status']);
        $this->assertEquals("Successfully deposited", $res['message']);

        $this->assertDatabaseHas('wallets', [
            'userid' => $userId,
            'balance' => "100.50",
            'source' => "cash",
            'note' => "initial",
        ]);

        $this->assertDatabaseHas('wallet_ledgers', [
            'userid' => $userId,
            'amount' => "100.50",
            'type' => "deposit",
            'source' => "cash",
            'destination' => "main",
            'receipt' => "R001",
            'note' => "initial",
        ]);
    }

    /** @test */
    public function deposit_increases_balance_and_creates_ledger_when_wallet_exists()
    {
        $userId = 11;

        Wallet::create([
            'userid' => $userId,
            'balance' => "10.00",
            'walletID' => 1,
            'source' => "",
            'note' => "",
        ]);

        $res = $this->service->deposit($userId, "2.25", "bank", "topup", "R002", "main");

        $this->assertEquals(1, $res['status']);

        $wallet = Wallet::where('userid', $userId)->first();
        $this->assertEquals("12.25", $wallet->balance);

        $this->assertDatabaseHas('wallet_ledgers', [
            'userid' => $userId,
            'amount' => "2.25",
            'type' => "deposit",
            'source' => "bank",
            'destination' => "main",
            'receipt' => "R002",
            'note' => "topup",
        ]);
    }

    /** @test */
    public function withdraw_returns_error_when_user_has_no_wallet()
    {
        $res = $this->service->withdraw(999, "1.00");

        $this->assertEquals(0, $res['status']);
        $this->assertEquals("User has no wallet", $res['message']);
    }

    /** @test */
    public function withdraw_returns_error_when_amount_exceeds_balance_and_does_not_create_ledger()
    {
        $userId = 12;

        Wallet::create([
            'userid' => $userId,
            'balance' => "5.00",
            'walletID' => 1,
            'source' => "",
            'note' => "",
        ]);

        $res = $this->service->withdraw($userId, "10.00", "too much", "R003", "atm", "main");

        $this->assertEquals(0, $res['status']);
        $this->assertEquals("Amount to be withdrawned is greater than balance", $res['message']);

        $wallet = Wallet::where('userid', $userId)->first();
        $this->assertEquals("5.00", $wallet->balance);

        $this->assertDatabaseMissing('wallet_ledgers', [
            'userid' => $userId,
            'type' => 'withdraw',
            'amount' => "10.00",
        ]);
    }

    /** @test */
    public function withdraw_decreases_balance_and_creates_withdraw_ledger()
    {
        $userId = 13;

        Wallet::create([
            'userid' => $userId,
            'balance' => "20.00",
            'walletID' => 1,
            'source' => "",
            'note' => "",
        ]);

        $res = $this->service->withdraw($userId, "7.50", "pay", "R004", "shop", "wallet");

        $this->assertEquals(1, $res['status']);
        $this->assertEquals("Successfully withdrawn", $res['message']);

        $wallet = Wallet::where('userid', $userId)->first();
        $this->assertEquals("12.50", $wallet->balance);

        $this->assertDatabaseHas('wallet_ledgers', [
            'userid' => $userId,
            'amount' => "7.50",
            'type' => "withdraw",
            'source' => "wallet",
            'destination' => "shop",
            'receipt' => "R004",
            'note' => "pay",
        ]);
    }

    /** @test */
    public function checkbalance_returns_balance_for_existing_wallet()
    {
        $userId = 14;

        Wallet::create([
            'userid' => $userId,
            'balance' => "99.99",
            'walletID' => 1,
            'source' => "",
            'note' => "",
        ]);

        $res = $this->service->checkbalance($userId);

        $this->assertEquals(1, $res['status']);
        $this->assertEquals("99.99", $res['balance']);
    }

    /** @test */
    public function getUserWalletledger_returns_ledgers_for_user()
    {
        $userId = 15;

        Wallet::create([
            'userid' => $userId,
            'balance' => "0.00",
            'walletID' => 1,
            'source' => "",
            'note' => "",
        ]);

        WalletLedger::create([
            'userid' => $userId,
            'amount' => "1.00",
            'type' => "deposit",
            'note' => "x",
            'source' => "s",
            'destination' => "d",
            'receipt' => "r",
        ]);

        $res = $this->service->getUserWalletledger($userId);

        $this->assertEquals(1, $res['status']);
        $this->assertCount(1, $res['ledger']);
        $this->assertEquals("deposit", $res['ledger'][0]->type);
    }
}
