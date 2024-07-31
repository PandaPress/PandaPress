const { exec } = require("child_process");
const packageJson = require("../package.json");

const buildScripts = Object.keys(packageJson.scripts).filter((script) =>
  script.startsWith("build:")
);

if (buildScripts.length === 0) {
  console.log("No build scripts found.");
  process.exit(0);
}

const commands = buildScripts.map((script) => `npm run ${script}`).join(" && ");

exec(commands, (err, stdout, stderr) => {
  if (err) {
    console.error(`Error: ${stderr}`);
    process.exit(1);
  }
  console.log(stdout);
});
