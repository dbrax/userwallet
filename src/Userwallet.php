<?php

namespace Epmnzava\Userwallet;

use Epmnzava\Userwallet\Models\Wallet;
use Epmnzava\Userwallet\Models\WalletLedger;
use Illuminate\Support\Str;

/**
 * Author: Emmanuel Paul Mnzava
 * Twitter: @epmnzava
 * Github:https://github.com/dbrax/userwallet
 * Email: epmnzava@gmail.com
 * 
 */


class Userwallet
{


    public function deposit($userid, $amount, $source = "", $note = "", $receipt = "",$destination="")
    {
        if ($this->userHasWallet($userid)) {
            $wallet = Wallet::find(Wallet::where('userid', $userid)->first()->id);

            $balance = bcadd((string) $wallet->balance, (string) $amount, 2);
            $wallet->balance = $balance;
            $wallet->source = $source;
            $wallet->note = $note;
            $wallet->save();

            $ledger = new WalletLedger;
            $ledger->amount = $amount;
            $ledger->userid = $userid;
            $ledger->receipt = $receipt;
            $ledger->destination=$destination;
            $ledger->source = $source;

            $ledger->type = "deposit";
            $ledger->note = $note;
            $ledger->save();

            return ["status" => 1, "ledger" => $ledger, "wallet" => $wallet, "message" => "Successfully deposited"];
        } else {
            $wallet = new Wallet;

            $balance = bcadd("0", (string) $amount, 2);
            $wallet->balance = $balance;
            $wallet->userid = $userid;
            $wallet->source = $source;
            $wallet->walletID = Wallet::count() + 1;
            $wallet->note = $note;
            $wallet->save();

            $ledger = new WalletLedger;
            $ledger->amount = $amount;
            $ledger->userid = $userid;
            $ledger->source=$source;
            $ledger->destination=$destination;
            $ledger->receipt = $receipt;
            $ledger->type = "deposit";
            $ledger->note = $note;
            $ledger->save();

            return ["status" => 1, "ledger" => $ledger, "wallet" => $wallet, "message" => "Successfully deposited"];
        }
    }

    public function userHasWallet($userid)
    {

        if (Wallet::where('userid', $userid)->count() > 0)
            return true;
        else
            return false;
    }

    public function withdraw($userid, $amount, $note = "", $receipt = "",$destination="",$source="")
    {
        if ($this->userHasWallet($userid)) {
            $wallet = Wallet::find(Wallet::where('userid', $userid)->first()->id);

         if (bccomp((string)$wallet->balance, (string)$amount, 2) === -1) 
                return ["status" => 0, "ledger" => [], "wallet" => [], "message" => "Amount to be withdrawned is greater than balance"];

            $balance =  bcsub($wallet->balance, $amount, 2);
            
            $wallet->balance = $balance;
            $wallet->note = $note;
            $wallet->save();

            $ledger = new WalletLedger;
            $ledger->amount = $amount;
            $ledger->userid = $userid;
            $ledger->source=$source;
            $ledger->destination=$destination;
            $ledger->receipt = $receipt;
            $ledger->type = "withdraw";
            $ledger->note = $note;
            $ledger->save();

            return ["status" => 1, "ledger" => $ledger, "wallet" => $wallet, "message" => "Successfully withdrawn"];
        } else
            return ["status" => 0, "wallet" => [], "message" => "User has no wallet"];
    }

    public function checkbalance($userid)
    {
        if ($this->userHasWallet($userid))
            return ["status" => 1, "balance" => Wallet::where("userid", $userid)->first()->balance, "message" => "Successfully fetched balance"];
        else
            return ["status" => 0, "balance" => null, "message" => "User has no balance"];
    }


    public function getUserWalletledger($userid)
    {

        if ($this->userHasWallet($userid))
            return ["status" => 1, "ledger" => WalletLedger::where("userid", $userid)->get(), "message" => "Successfully fetched user ledger"];
        else
            return ["status" => 0, "ledger" => null, "message" => "User doesnot exist"];
    }
}
