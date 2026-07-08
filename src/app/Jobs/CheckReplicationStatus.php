<?php

namespace App\Jobs;

use App\Repositories\Admin\ClientConnectionLogRepository;
use App\Repositories\Admin\ClientRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CheckReplicationStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $clientConnectionLogRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @param ClientRepository $clientRepository
     * @param ClientConnectionLogRepository $clientConnectionLogRepository
     * @return void
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function handle(ClientRepository $clientRepository, ClientConnectionLogRepository $clientConnectionLogRepository)
    {
        $this->clientConnectionLogRepository = $clientConnectionLogRepository;

//        $clients = $clientRepository->findWhereNotIn('id', [1])->all();
        $replications = \DB::select(\DB::raw("SHOW SLAVE HOSTS"));
//        dd($replications);
        $connected_servers = [1];
        foreach ($replications as $replication) {
            $connected_servers[] = $replication->Server_id;
        }


        // Newly Disconnected Clients
        $disconnected = $clientRepository->model()::whereNotIn('id', $connected_servers)->where('connection_status', 1);
        $dcServers    = $disconnected->get()->pluck('id')->toArray();
        $this->addLog($dcServers, 0);
        $disconnected->update(['connection_status' => 0]);

        // Newly Connected Clients
        $connected       = $clientRepository->model()::whereIn('id', $connected_servers)->where('connection_status', 0);
        $connecteServers = $connected->get()->pluck('id')->toArray();
        $this->addLog($connecteServers, 1);
        $connected->update(['connection_status' => 1]);
    }


    public function addLog($servers, $status)
    {
        $lastRecords = $this->clientConnectionLogRepository->model()::whereIn('client_id', $servers)->orderBy('id', 'desc')->get();
        foreach ($servers as $server) {
            // Update Last Record's Time
            $last = $lastRecords->where('client_id', $server)->first();
            if ($last) {
                $last->seconds_until_next = time() - strtotime($last->created_at);
                $last->save();
            }

            // Add New Log Entry
            $this->clientConnectionLogRepository->create([
                'client_id'          => $server,
                'status'             => $status,
                'seconds_until_next' => 0
            ]);
        }
    }
}
