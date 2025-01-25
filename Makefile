.PHONY: run

run:
	@echo "🚀 Starting app. Please wait..."
	@$(MAKE) d.first-steps
	@sleep .5
	@docker compose up -d --build --force-recreate
	@echo "App started. Connecting a shell to the docker container..."
	@sleep .5
	@$(MAKE) d.shell
	@$(MAKE) d.down
	
d.down:
	@echo "⚠️ Stopping app. Please wait..."
	@docker compose down
	@echo "App stopped. Goodbye ✋"

d.shell:
	@docker compose exec -it app bash -c "echo 'Shell connected 🚀' && echo 'You can run \"exit\" to close this shell' && bash"
	@echo "Shell session terminated!"

d.first-steps:
	@echo "Installing dependencies..."
	@docker compose run --rm composer install
	@sleep .5
	@echo "Dependencies installed. Generating key..."
	@docker compose run --rm artisan key:generate
	@sleep .5
	@echo "Key generated."
