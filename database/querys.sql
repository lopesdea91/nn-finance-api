SELECT * FROM laravel9.finance_item order by 1 desc;
SELECT * FROM laravel9.finance_tags order by 1 desc;
## DELETE FROM laravel9.finance_item;
## DELETE FROM `laravel9`.`finance_item` WHERE (`id` < '26');


SELECT * FROM laravel9.migrations;

SELECT * FROM laravel9.finance_tags;
SELECT * FROM laravel9.finance_origin;


SELECT * FROM finance_item where id = 1;
SELECT * FROM finance_item_obs where item_id = 1;
SELECT * FROM finance_item_tag where item_id = 1;
SELECT * FROM finance_item where date <= '2023-02-27' order by date desc;
SELECT * FROM finance_item i join finance_item_obs o on i.id = o.item_id;


SELECT * FROM finance_item_obs;
SELECT * FROM finance_item order by id desc;
SELECT * FROM finance_item_tag;
SELECT * FROM finance_tag;
SELECT * FROM finance_type;
SELECT * FROM finance_category;
SELECT * FROM finance_category_closure;
SELECT * FROM finance_group;
SELECT * FROM finance_origin where id = 1;
SELECT * FROM finance_origin_type;
SELECT * FROM finance_wallet_consolidate_month;
SELECT * FROM finance_wallet;
SELECT * FROM finance_list;
SELECT * FROM finance_invoice;
SELECT * FROM finance_origin_type;
SELECT * FROM finance_type;
SELECT * FROM finance_status;
SELECT * FROM laravel9.personal_access_tokens;
SELECT * FROM laravel9.password_resets;


## ------------------



DROP TABLE finance_item_description;
DROP TABLE finance_item_repeat;
DROP TABLE finance_item_tag;
DROP TABLE finance_invoice_item;
DROP TABLE finance_list_item;


DROP TABLE finance_list;
DROP TABLE finance_invoice;
DROP TABLE finance_item;

DROP TABLE finance_tag;

DROP TABLE finance_category_closure;
DROP TABLE finance_category;
DROP TABLE finance_group;
DROP TABLE finance_origin;
DROP TABLE finance_origin_type;
DROP TABLE finance_type;
DROP TABLE finance_status;
DROP TABLE finance_wallet_consolidate_month;
DROP TABLE finance_wallet;

DROP TABLE failed_jobs;
DROP TABLE migrations;
DROP TABLE password_resets;
DROP TABLE personal_access_tokens;
DROP TABLE users;
