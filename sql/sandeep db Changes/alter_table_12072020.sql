ALTER TABLE product_service ADD generaltermscondition text DEFAULT NULL;


ALTER TABLE product_service_claim ADD on_approved smallint(1) DEFAULT NULL;
ALTER TABLE product_service_claim ADD on_api smallint(1) NOT NULL DEFAULT 1;


ALTER TABLE product_service_claim ADD multiple_price smallint(1) NOT NULL DEFAULT 0;
