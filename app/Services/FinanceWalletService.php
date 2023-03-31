<?php

namespace App\Services;

use App\Models\FinanceItemModel;
use App\Models\FinanceWalletModel;
use App\Services\Base\BaseService;
use App\Repository\FinanceWalletRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinanceWalletService extends BaseService
{
	protected $repository;

	public function __construct()
	{
		$this->repository = new FinanceWalletRepository;
	}

	public function paginate($args)
	{
		$user_id = Auth::user()->id;

		return $this->repository->paginate([
			'query' => $args['query'],
			'where' => [
				['user_id', '=', $user_id]
			],
			'whereHas' => [],
		]);
	}
	public function all($args)
	{
		$user_id = Auth::user()->id;

		return parent::all([
			'query' => $args['query'],
			'where' => [
				'user_id' => $user_id
			],
			'whereHas' => [],
		]);
	}
	public function create($fields)
	{
		$createField = [
			'description' => $fields['description'],
			'json'        => '{}',
			'enable'      => '1',
			'panel'       => '0',
			'user_id'     => Auth::user()->id
		];

		return $this->repository->create($createField);
	}
	public function update($id, $fields)
	{
		$where = [
			'id'      => $id,
			'user_id' => Auth::user()->id
		];

		$updateField = [
			'description' => $fields['description'],
			'json'        => $fields['json'],
			'enable'      => $fields['enable'],
			'panel'       => $fields['panel'],
		];

		return $this->repository->update($where, $updateField);
	}
	public function periodsData($fields)
	{
		// $period    = $fields['period'];
		$wallet_id = $fields['wallet_id'];

		$items = FinanceItemModel::where([
			'wallet_id' => $wallet_id,
			'enable'    => 1,
		])
			->selectRaw(DB::raw("DATE_FORMAT(date, '%Y') as year, DATE_FORMAT(date, '%m') as month, DATE_FORMAT(date, '%Y-%m') as period, DATE_FORMAT(date, '%m/%Y') as label"))
			// ->groupBy(DB::raw("DATE_FORMAT(date, '%Y-%m')"))
			->orderBy('period', 'DESC')
			->get()
			->toArray();

		if (key_exists('format', $fields) && $fields['format'] === 'group-periods') {
			$periods = [];

			foreach ($items as $key_item => $item) {
				$year   = $item['year'];
				$month  = $item['month'];

				## add YEAR when not exist in $periods
				if (!key_exists($year, $periods)) {
					$periods[$year] = [
						'year'    => $year,
						'months'  => []
					];
				}

				## add MONTH when not exist in $periods
				if (!key_exists($month, $periods[$year]['months'])) {
					$periods[$year]['months'][$month] = [];
				}

				$periods[$year]['months'][$month] = [
					"period"  => $item['period'],
					"label"   => $item['label'],
				];

				unset($items[$key_item], $key_item, $item, $year, $month);
			}

			// ordena as key ano por desc
			krsort($periods);

			// remove array com key ano
			$periods = array_values($periods);

			// formata month com key mes para array
			foreach ($periods as $key_period => $period) {
				$periods[$key_period]['months'] = array_values($period['months']);

				unset($key_period, $period);
			}

			$items = $periods;

			unset($periods);
		}

		return $items;
	}
}
