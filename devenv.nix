{ pkgs, lib, config, inputs, ... }:

{
  scripts.a.exec = "php artisan $@";
  scripts.aa.exec = "docker compose exec web php artisan $@";
  scripts.atf.exec = "aa test --filter $@";

  processes = {
    containers.exec = "docker compose up";
    queues.exec = "sleep 5s && aa queue:listen --tries=1";
    frontend.exec = "pnpm dev";
  };

  dotenv.disableHint = true;

  packages = with pkgs; [
    git-cliff
    actionlint
  ];

  languages = {
    php = {
      enable = true;
      package = pkgs.php83.buildEnv {
        extensions = { all, enabled }: with all; enabled ++ [
          bz2
          curl
          dom
          filter
          fileinfo
          gd
          iconv
          imagick
          intl
          mbstring
          openssl
          pdo
          pdo_mysql
          pdo_sqlite
          session
          sodium
          sqlite3
          tidy
          tokenizer
          xdebug
          xmlwriter
          zip
          zlib
        ];
        extraConfig = ''
          xdebug.mode=debug
          xdebug.discover_client_host
        '';
      };
    };

    javascript = {
      enable = true;
      pnpm.enable = true;
    };
  };
}
