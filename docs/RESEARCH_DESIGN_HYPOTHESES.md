# Research-informed design hypotheses

English | [日本語](RESEARCH_DESIGN_HYPOTHESES.ja.md)

2026-09-29. Revision 1. A working evidence register, not evidence that Dual Learning improves learning outcomes.

## Required milestone step

Borrow principles supported by existing research as **design hypotheses**, rather than relying only on intuition. Before each prototype, record a source, the population/task and outcome studied, the limits of transfer, a proposed application, possible adverse effects, and a way to observe it. Distinguish the published finding, our interpretation, and evidence collected in this product. Revise or abandon the hypothesis when observations conflict with it.

The initial sources below are selected empirical studies, not a systematic review or a claim of consensus about every application. Check broader evidence and relevant replications before making stronger claims. Instructional feedback and immediate confirmation of a UI action are different: the latter does not justify always revealing answers immediately.

## Initial evidence and hypotheses

### R1: Avoid unnecessary integration effort

Chandler and Sweller (1991), *Cognitive Load Theory and the Format of Instruction*, reports six experiments with electrical-engineering and biology instructional materials. Integrating information helped when separate sources needed to be understood together; integration was not universally beneficial. [Publisher abstract](https://www.tandfonline.com/doi/abs/10.1207/s1532690xci0804_2).

**H1 — indirect application:** put a saved material's identity, visibility settings and review/edit actions together so authors need less navigation to understand their work. This transfers an instructional-material finding to an authoring interface; that transfer is untested. Operational ease might support more careful revision, which might benefit learning, but neither causal link is established here. Extra status text may instead distract. Observe correct explanations of visibility, successful return to the same material, hesitation and help requests; do not infer cognitive load or learning gains from click counts alone.

### R2: Support retrieval, not only rereading

Roediger and Karpicke (2006), *Test-Enhanced Learning*, compared free recall and restudy of prose passages in two experiments with students. Restudy performed better at a short delay, while retrieval practice benefited delayed retention. [Original abstract](https://www.psychologicalscience.org/journals/psychological-science/j.1467-9280.2006.01693.x/).

**H2 — later candidate:** offer optional prompts to explain or recall before reopening an explanation. Clicking “complete” is not retrieval. Avoid compulsory quizzes and unsupported transfer to every age, subject or assessment. Check whether prompts invite meaningful retrieval and allow correction; retention requires a separate delayed assessment. This milestone does not implement H2.

### R3: Enable later revisiting

Cepeda et al. (2008), *Spacing Effects in Learning: A Temporal Ridgeline of Optimal Retention*, examined factual learning and later review/test intervals with over 1,350 participants. The useful spacing interval depended on the intended retention interval. [Original-study abstract](https://pubmed.ncbi.nlm.nih.gov/19076480/).

**H3 — later candidate:** keep completed materials reachable and allow contextual invitations to revisit. Access alone does not establish spaced practice or improved retention. Do not impose a universal review interval. Observe whether learners can find prior materials; evaluating retention requires separate evidence. Excessive reminders may interrupt or discourage learners. This milestone does not implement H3.

## First prototype and next decisions

Implement H1 first, under the [authoring milestone](AUTHORING_UX_MILESTONE.md). The initial gap found by code review is a material list that offers “View” and “Edit” without item-level visibility settings or a clear distinction between teacher viewing and student access. This is a developer finding, not observed novice behaviour.

The first prototype groups existing standard actions and visibility settings. It leaves Moodle authoritative for permissions and content and requires neither LessonMark nor the Dual Learning theme. Functional checks establish routes, permissions and preservation of hidden drafts. Actual novice evaluation, comparison with Topics, and learning-outcome validation remain outstanding. A safe functional prototype may proceed without claiming those evaluations have happened.

For future changes, extend this register with H4, H5, etc.; link each implementation and evaluation record to its hypothesis. Research informs choices and their limits; it must not become decoration added after implementation.
